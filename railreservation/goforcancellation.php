<?php
	require 'guard.php';
	require 'dblink.php';
	ini_set('display_errors',1);
	error_reporting(E_ALL | E_STRICT); 
	$username = strtolower($_SESSION['userid']);
	$num = ord($username[0])-96;
	$pnr = $_POST['pnrnum'];

	
	$query = "SELECT *
				FROM tkt_$num
				WHERE userid = '$username'
					AND pnr = '$pnr'
				";
	
	$tickets = pg_query($query);
	$ticket = pg_fetch_array($tickets);

	
	$query = "SELECT dayoffset, stcode, stno, dtarr, dtdep
				FROM Stand
				WHERE trainno='".$ticket['trainno']."'
					AND	(stno = '".$ticket['fromstno']."'
							OR stno = '".$ticket['tostno']."')";
							
	$results = pg_query($query);
	while($result = pg_fetch_array($results)){
		
		if($result['stno']==$ticket['fromstno']){
			$fromstcode = $result['stcode'];
			$fromoffset = $result['dayoffset'];
			$fromdeptime = $result['dtdep'];
		}
		else if($result['stno']==$ticket['tostno']){
			$tostcode = $result['stcode'];
			$tooffset = $result['dayoffset'];
			$toarrtime = $result['dtarr'];
		}
	}
	
	$srcdate = date('Ymd', strtotime($ticket['dtjour']." -".($fromoffset-1)." days"));
	$jrndate = date('Y-m-d', strtotime($ticket['dtjour']));
	$findate = date('Y-m-d', strtotime($ticket['dtjour']." +".($tooffset-$fromoffset)." days"));
	$query = "SELECT *
				FROM trip_$srcdate
				WHERE pnr = $pnr
				ORDER BY resid ASC";
				
	$passengers = pg_query($query);
	$ticket = "<table id='goforcancellationtable'>
			<tr><th>Go for Cancellation</th></tr>
			<tr><td>PNR:$pnr</td></tr>
			<tr><td>Cost: Rs.".$ticket['cost']."</td></tr>
			<tr><td>Board:".$fromstcode."</td></tr>
			
			<tr><td>From:".$fromstcode."</td></tr>
			<tr><td>Schd. Dep.:".$jrndate." ".$fromdeptime."</td></tr>
			<tr><td>To:".$tostcode."</td></tr>
			
			<tr><td>Schd. Arr.:".$findate." ".$toarrtime."</td></tr>
			<tr><td>Train no:".$ticket['trainno']."</td></tr>
			<tr><td>Journey date:".date('Y-m-d', strtotime($ticket['dtjour']))."</td></tr>
			<tr><td>Distance:".$ticket['distance']."</td></tr>
		</tr></table>";
	
	$ticket .= "<form><table><tr><th>Name</th><th>Age</th><th>Gender</th><th>Seat</th><th>Cancel?</th></tr>";
	$i=1;
	while($passenger= pg_fetch_array($passengers)){
		$ticket .= "<tr>
					<td>".$passenger['passname']."</td>
					<td>".$passenger['passage']."</td>
					<td>".$passenger['passgender']."</td>";
		if($passenger['cancelled']=='Y'){
			$ticket .= "<td>CAN</td>";
			$ticket .= "<td><input type='checkbox' id='cancel$i' style='visibility:hidden;'></td>";
		}else if($passenger['status']=='CNF'){
			$ticket .= "<td>".$passenger['status']." ".$passenger['coach']." ".$passenger['seat']."</td>";
			$ticket .= "<td><input type='checkbox' id='cancel$i'></td>";
		}else if($passenger['status']=='RAC'){
			$query = "SELECT nseats, maxseats
						FROM stats_$srcdate
						WHERE trainno=".$passenger['trainno']."
							AND pool=".$passenger['pool']."
							AND chtype='".$passenger['coach']."'
							AND quota='".$passenger['quota']."'
						";
			
			$result=pg_fetch_array(pg_query($query));
			$ticket .= "<td>".$passenger['status']." ".($result['maxseats']-$result['nseats'])." / ".$passenger['coach']." ".$passenger['seat']."</td>";
			$ticket .= "<td><input type='checkbox' id='cancel$i'></td>";
		}else if($passenger['status']=='WL'){
			$query = "SELECT nseats, maxseats
						FROM stats_$srcdate
						WHERE trainno=".$passenger['trainno']."
							AND pool=".$passenger['pool']."
							AND chtype='".$passenger['coach']."'
							AND quota='".$passenger['quota']."'
						";
			
			$result=pg_fetch_array(pg_query($query));
			$ticket .= "<td>".$passenger['status']." ".$passenger['coach']." ".($result['maxseats']-$result['nseats'])."</td>";
			$ticket .= "<td><input type='checkbox' id='cancel$i'></td>";
		}
		$ticket .= "</tr>";
		$i++;			
	}
	$passnum= $i-1;
	$ticket .= "<tr><td colspan='5' align='center'><input type='button' class='sbuttonclass' onclick='cancel($pnr, $srcdate,$passnum)' value='Cancel'></td></tr>";
	$ticket .= "</table></form>";
	echo $ticket;
?>
