<?php
	require 'guard.php';
	require 'dblink.php';
  ini_set('display_errors',1);
  error_reporting(E_ALL | E_STRICT); 
	$_SESSION['quota'] = $quota = $_POST['quota'];
	$_SESSION['jrndate'] = $date = date('Y-m-d', strtotime($_POST['jrndate']));
	$_SESSION['from']= strtoupper($_POST['from']);
	$_SESSION['to']= strtoupper($_POST['to']);
	$day=date("D",strtotime($date));

	if($day=='Mon')
		$dayn=1;
	else if($day=='Tue')
		$dayn=2;
	else if($day=='Wed')
		$dayn=3;
	else if($day=='Thu')
		$dayn=4;
	else if($day=='Fri')
		$dayn=5;
	else if($day=='Sat')
		$dayn=6;
	else if($day=='Sun')
		$dayn=0;
	$query="
		WITH t1 AS(
			SELECT * FROM Stand WHERE stcode='".$_SESSION['from']."' OR stcode='".$_SESSION['to']."'
			), t2 AS(
				SELECT tfrom.trainno as trainno,
						tfrom.stno as fromstno,
						tfrom.stcode as fromstcode,
						tfrom.dtarr as fromdtarr,
						tfrom.dtdep as fromdtdep,
						tfrom.dayoffset as fromdayoffset,
						tfrom.distance as fromdistance,
						tto.stno as tostno,
						tto.stcode as tostcode,
						tto.dtarr as todtarr,
						tto.dtdep as todtdep,
						tto.dayoffset as todayoffset,
						tto.distance as todistance 
				FROM t1 as tfrom,t1 as tto 
				WHERE tfrom.stcode='".$_SESSION['from']."' AND tto.stcode='".$_SESSION['to']."' AND tfrom.stno<tto.stno AND tfrom.trainno=tto.trainno  
			), t3 AS(					
				SELECT *
				FROM t2 NATURAL JOIN train_days
				WHERE (t2.fromdayoffset+days-1)%7=".strval($dayn)." 
			), t4 AS(
				SELECT t3.trainno as tno, Pool.stno as pno
				FROM t3, Pool
				WHERE t3.trainno = Pool.trainno AND t3.tostno <= Pool.stno
			), t5 AS(
				SELECT tno, trtype, MIN(pno) as poolno, trname
				FROM t4, Train
				WHERE Train.trainno = tno
				GROUP BY tno, trname, trtype
			)
		SELECT *
		FROM t3, t5
		WHERE t3.trainno = t5.tno;
		";
	$result=pg_query($con, $query);
	$rownos=pg_num_rows($result);
	
	if(!$rownos){
		echo "<table id='tabletrains'><tr><td>No trains found</td></tr></table>";
		exit();
	}
	else {
		
		$string = "<form>";
		$string .= "<table id='tabletrains'>";
		$string.="<tr>
				<th>Train Number</th>
				<th>Train Name</th>
				<th>Date of Boarding</th>
				<th>Departure Time</th>
				<th>Date of Reaching</th>
				<th>Arrival Time</th>
				<th>Distance of Journey</th>
				<th>SL</th>
				<th>A</th></th>
				<th>B</th>
				<th>C</th>
				<th>CC</th>
				<th>G</th>
				<th>Other</th>
			</tr>";
		while ($row = pg_fetch_array($result)) {
			$_SESSION['distance']=$row['todistance']-$row['fromdistance'];
			$_SESSION['fromstcode']=$row['fromstcode'];
			$_SESSION['tostcode']=$row['tostcode'];
			
			$findate = date('Y/m/d', strtotime($_POST['jrndate']."+".($row['todayoffset']-$row['fromdayoffset'])."days"));
			$srcdate = date('Y-m-d', strtotime($_POST['jrndate']." -".($row['fromdayoffset']-1)." days"));
			
			
			$string.="<tr>";
			$string .= "<td>".$row['trainno']."</td>";
			$string .= "<td>".$row['trname']."</td>";
			$string .= "<td>".$_POST['jrndate']."</td>";//dep from boarding stn date
			$string .= "<td>".$row['fromdtdep']."</td>";//dep from boarding stn time
			$string .= "<td>".$findate."</td>";//arr at dest stn date
			$string .= "<td>".$row['todtarr']."</td>";//arr at dest stn time
			$string .= "<td>".($row['todistance']-$row['fromdistance'])."</td>";
			
			$string .= "<td><input type='radio' name='avlbradio' onclick=\"getAvailability('"
						.$row['trainno']."', '"
						.$row['trtype']."', '"
						.$srcdate."', '"
						.$row['poolno']."', '"
						.$_POST['quota']."', '"
						."SL"."', '"
						.$row['fromdtdep']."', '"
						.$findate."', '"
						.$row['todtarr']."', '"
						.$row['fromstno']."', '"
						.$row['tostno']."') \" ></td>";
			$string .= "<td><input type='radio' name='avlbradio' onclick=\"getAvailability('"
						.$row['trainno']."', '"
						.$row['trtype']."', '"
						.$srcdate."', '"
						.$row['poolno']."', '"
						.$_POST['quota']."', '"
						."A"."', '"
						.$row['fromdtdep']."', '"
						.$findate."', '"
						.$row['todtarr']."', '"
						.$row['fromstno']."', '"
						.$row['tostno']."') \" ></td>";
			$string .= "<td><input type='radio' name='avlbradio' onclick=\"getAvailability('"
						.$row['trainno']."', '"
						.$row['trtype']."', '"
						.$srcdate."', '"
						.$row['poolno']."', '"
						.$_POST['quota']."', '"
						."B"."', '"
						.$row['fromdtdep']."', '"
						.$findate."', '"
						.$row['todtarr']."', '"
						.$row['fromstno']."', '"
						.$row['tostno']."') \" ></td>";
			$string .= "<td><input type='radio' name='avlbradio' onclick=\"getAvailability('"
						.$row['trainno']."', '"
						.$row['trtype']."', '"
						.$srcdate."', '"
						.$row['poolno']."', '"
						.$_POST['quota']."', '"
						."C"."', '"
						.$row['fromdtdep']."', '"
						.$findate."', '"
						.$row['todtarr']."', '"
						.$row['fromstno']."', '"
						.$row['tostno']."') \" ></td>";
			$string .= "<td><input type='radio' name='avlbradio' onclick=\"getAvailability('"
						.$row['trainno']."', '"
						.$row['trtype']."', '"
						.$srcdate."', '"
						.$row['poolno']."', '"
						.$_POST['quota']."', '"
						."CC"."', '"
						.$row['fromdtdep']."', '"
						.$findate."', '"
						.$row['todtarr']."', '"
						.$row['fromstno']."', '"
						.$row['tostno']."') \" ></td>";
			$string .= "<td><input type='radio' name='avlbradio' onclick=\"getAvailability('"
						.$row['trainno']."', '"
						.$row['trtype']."', '"
						.$srcdate."', '"
						.$row['poolno']."', '"
						.$_POST['quota']."', '"
						."G"."', '"
						.$row['fromdtdep']."', '"
						.$findate."', '"
						.$row['todtarr']."', '"
						.$row['fromstno']."', '"
						.$row['tostno']."') \" ></td>";
			$string .= "<td>"."Other"."</td>";
			$string.="</tr>";
		}
		$string.="</table></form>";
	}
	echo $string;
