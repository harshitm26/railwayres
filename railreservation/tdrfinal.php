<?php
	require 'guard.php';
	require 'dblink.php';
	ini_set('display_errors',1);
	error_reporting(E_ALL | E_STRICT); 
	$srcdate = $_POST['srcdate'];
	$pnr = $_POST['pnrnum'];
	$reason = $_POST['reason'];
	$userid = $_SESSION['userid'];
	$query = "SELECT *
				FROM trip_$srcdate
				WHERE pnr=$pnr
				ORDER BY resid ASC
			"; 
	
	$passengers = pg_query($query);
	$dtfile = date('Y-m-d H:i:s');
	$query = "INSERT INTO tdr
					VALUES(
						DEFAULT,
						'$reason',
						current_timestamp,
						'$userid',
						0
					)
				RETURNING tdrno
			";
	$results = pg_query($query);		
	$result = pg_fetch_array($results);
	$tdrno = $result['tdrno'];
	$i=0;
	while($passenger = pg_fetch_array($passengers)){
		$i=$i+1;
		if($_POST["tdr$i"]==0){
			//echo "NOT Filing TDR for $i<br/>";
			continue;
		}
		$resid = $passenger['resid'];
		$query = "INSERT INTO tdrresid
						VALUES(
							$tdrno,
							$resid
						)
				";
		//echo $query;		
		pg_query($query);		
	}
	echo "TDR Filed Successfully!";
