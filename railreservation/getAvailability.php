<?php
	require 'guard.php';
	require 'dblink.php';
	ini_set('display_errors',1);
	error_reporting(E_ALL | E_STRICT); 
	//trainno, trtype, srcdate, poolno, quota, chtype, fromdtdep, findate, todtarr, fromstno, tostno
	$trainno = $_SESSION['trainno']= $_POST['trainno'];
	$trtype = $_SESSION['trtype']= $_POST['trtype'];
	$date = $_POST['srcdate'];
	$_SESSION['srcdate'] = $date;
	$poolno = $_SESSION['poolno']=$_POST['poolno'];
	$quota = $_SSSION['quota'] = $_POST['quota'];
	$chtype = $_SESSION['chtype']= $_POST['chtype'];
	$fromdtdep = $_SESSION['fromdtdep'] = $_POST['fromdtdep'];
	$findate = $_SESSION['findate'] = $_POST['findate'];
	$todtarr = $_SESSION['todtarr'] = $_POST['todtarr'];
	$fromstno = $_SESSION['fromstno'] = $_POST['fromstno'];
	$tostno = $_SESSION['tostno'] = $_POST['tostno'];

	$query = 	"SELECT costperkm
				FROM Fare
				WHERE trtype = '$trtype'
					AND chtype = '$chtype'
					AND quota = '$quota'
				";
	$results = pg_query($query);
	$row = pg_fetch_array($results);
	$_SESSION['fare'] = $row['costperkm']*$_SESSION['distance'];




	$query = 	"SELECT status, nseats, maxseats
				FROM stats_".date('Ymd', strtotime($_SESSION['srcdate']))."
				WHERE trainno = $trainno
					AND chtype= '$chtype'
					AND pool = $poolno
					AND quota = '$quota'
				";
	$results = pg_query($query);
	
	while($row = pg_fetch_array($results)){
		if($row['status']=='CNF'){
			$ncnfseats = $row['nseats'];
			$maxcnfseats = $row['maxseats'];
		}
		else if($row['status']=='RAC'){
			$nracseats = $row['nseats'];
			$maxracseats = $row['maxseats'];
		}
		else if($row['status']=='WL'){
			$nwlseats = $row['nseats'];
			$maxracseats = $row['maxseats'];
		}
	}

	$string = "<form id = 'bookForm'><table id='avlbtable'>";
	$string .= "<tr><th>Availability</th></tr>";
	$string.= "<tr><td>$trainno</td></tr>";
	$string.= "<tr><td>$quota</td></tr>";
	$string.= "<tr><td>$chtype</td></tr>";
	$string.= "<tr><td>".$_SESSION['jrndate']."</td></tr>";
	$string.= "<tr><td>Rs. ".$_SESSION['fare']."</td></tr>";
	$string.= "<tr><td>";
	if($ncnfseats!=0){
		$string .= $ncnfseats." AVAILABLE<br/><input type='button' value='Book' class='sbuttonclass' onclick='showBookForm()'>";
	}else if($nracseats!=0){
		$string .= ($maxracseats-$nracseats)." RAC<br/><input type='button' value='Book' class='sbuttonclass' onclick='showBookForm()'>";
	}else if($wlseats!=0){
		$string .= ($maxwlseats-$nwlseats)." WL<br/><input type='button' value='Book' class='sbuttonclass' onclick='showBookForm()'>";
	}else{
		$string .= "REGRET";
	}
	$string .= "</td></tr>";
	$string .= "</table></form>";
	echo $string;

	
