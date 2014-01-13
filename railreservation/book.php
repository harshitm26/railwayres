<?php
	require 'guard.php';
	require 'dblink.php';
	ini_set('display_errors',1);
	error_reporting(E_ALL | E_STRICT); 
	
	$username = strtolower($_SESSION['userid']);
	
	$num = ord($username[0])-96;
	
	$query = "INSERT INTO tkt_$num VALUES(
				DEFAULT, '"
				.$_SESSION['fare']."', '"
				.$_SESSION['fromstno']."', '"
				.$_SESSION['fromstno']."', '"
				.$_SESSION['tostno']."', '"
				.$_SESSION['trainno']."', '"
				.$_SESSION['jrndate']."', '"
				.date('Y-m-d')."', '"
				.$_SESSION['userid']."', '"
				.$_SESSION['distance']."') RETURNING pnr";
	$results = pg_query($query);
	if(!$results){
		echo "Booking Unsuccessful";
		exit();
	}else{
		$result = pg_fetch_array($results);
		$pnr = $result['pnr'];
		$ticket ="<table id='tickettable'<tr><th colspan='4'>Ticket</th></tr>";
		$ticket .= "<th>Name</th><th>Age</th><th>Gender</th><th>Seat</th></tr>";
		for($i=1; $i<=6; $i++){
			if(!isset($_POST['passname'.$i]) || empty($_POST['passname'.$i])){
				continue;
			}
			$query = 	"SELECT status, nseats 
						FROM stats_".date('Ymd', strtotime($_SESSION['srcdate']))."
						WHERE trainno = '".$_SESSION['trainno']."'
							AND chtype = '".$_SESSION['chtype']."'
							AND pool = '".$_SESSION['poolno']."'
							AND quota = '".$_SESSION['quota']."'";
			$results = pg_query($query);
			while($row = pg_fetch_array($results)){
				if($row['status']=='CNF') $cnfseats = $row['nseats'];
				else if($row['status']=='RAC') $racseats = $row['nseats'];
				else if($row['status']=='WL') $wlseats = $row['nseats'];
			}
			
			if($cnfseats!=0){
				$query = "UPDATE stats_".date('Ymd', strtotime($_SESSION['srcdate']))."
							SET nseats = nseats -1
							WHERE trainno = '".$_SESSION['trainno']."'
								AND chtype = '".$_SESSION['chtype']."'
								AND pool = '".$_SESSION['poolno']."'
								AND quota = '".$_SESSION['quota']."'
								AND status = 'CNF'";
				pg_query($query);
				
				$query =	"SELECT SUM(nsleep) as nsleep, SUM(ngen) as ngen, SUM(nfac) as nfac, SUM(nsac) as nsac, SUM(ntac) as ntac, SUM(ncc) as ncc
							FROM Pool
							WHERE trainno = ".$_SESSION['trainno']."
							GROUP BY trainno";
				$results = pg_query($query);
				$coachmap = pg_fetch_array($results);
				$lower =0;
				if($_SESSION['chtype']=='A'){
					$lower += 0;
					$upper = $lower + $coachmap['nfac'];
				}else if($_SESSION['chtype']=='B'){
					$lower +=$coachmap['nfac'];
					$upper = $lower + $coachmap['nsac'];
				}else if($_SESSION['chtype']=='C'){
					$lower +=$coachmap['nfac']+$coachmap['nsac'];
					$upper = $lower + $coachmap['ntac'];
				}else if($_SESSION['chtype']=='SL'){
					$lower +=$coachmap['nfac']+$coachmap['nsac']+$coachmap['ntac'];
					$upper = $lower + $coachmap['nsleep'];
				}else if($_SESSION['chtype']=='CC'){
					$lower +=$coachmap['nfac']+$coachmap['nsac']+$coachmap['ntac']+$coachmap['nsleep'];
					$upper = $lower + $coachmap['ncc'];
				}else if($_SESSION['chtype']=='G'){
					$lower +=$coachmap['nfac']+$coachmap['nsac']+$coachmap['ntac']+$coachmap['nsleep']+$coachmap['ncc'];
					$upper = $lower + $coachmap['ngen'];
				}
				$lower *= SEATSPERCOACH;
				$upper *= SEATSPERCOACH;
				$query = 	"SELECT seatno1, seatno2
							FROM seatavlb_".str_replace('-', '', $_SESSION['srcdate'])."
							WHERE seatno1>=".$lower."
								AND seatno2<=".$upper."
								AND seatno1 <= seatno2
								AND trainno = ".$_SESSION['trainno']."
							LIMIT 1";
				//echo $query;
				$results = pg_query($query);
				$result = pg_fetch_array($results);
				$seatassigned = $result['seatno1'];
				if($result['seatno1']<$result['seatno2']){
					$query = 	"UPDATE seatavlb_".str_replace('-', '', $_SESSION['srcdate'])."
								SET seatno1 = seatno1+ 1
								WHERE seatno1 = ".$result['seatno1']."
									AND trainno = '".$_SESSION['trainno']."'";
					pg_query($query);
				}else{
					$query = 	"DELETE FROM seatavlb_".str_replace('-', '', $_SESSION['srcdate'])."
								WHERE seatno1 = ".$result['seatno1']."
									AND trainno = ".$_SESSION['trainno'];
					// $query;	
					pg_query($query);
				}
				$query = "INSERT INTO trip_".str_replace('-', '', $_SESSION['srcdate'])." VALUES(
							DEFAULT,
							$pnr, '".
							$_SESSION['trainno']."',
							'N', '".
							$_SESSION['poolno']."','".
							$_SESSION['quota']."',
							'CNF', '".
							$_SESSION['chtype']."', '".
							$seatassigned."', '".
							$_POST['passname'.$i]."', '".
							$_POST['passgender'.$i]."', '".
							$_POST['passage'.$i]."')";
				//($query);
				pg_query($query);
				$ticket .= "<tr><td>".$_POST['passname'.$i]."</td><td>".$_POST['passage'.$i]."</td><td>".$_POST['passgender'.$i]."</td><td>CNF ".$seatassigned."</td></tr>";
			}else if($racseats!=0){
				//RAC RESERVATION----------------------------
				$query = "UPDATE stats_".str_replace('-', '', $_SESSION['srcdate'])."
							SET nseats = nseats -1
							WHERE trainno = '".$_SESSION['trainno']."'
								AND chtype = '".$_SESSION['chtype']."'
								AND pool = '".$_SESSION['poolno']."'
								AND quota = '".$_SESSION['quota']."'
								AND status = 'RAC'";
				pg_query($query);
				
				$query =	"SELECT SUM(nsleep) as nsleep, SUM(ngen) as ngen, SUM(nfac) as nfac, SUM(nsac) as nsac, SUM(ntac) as ntac, SUM(ncc) as ncc
							FROM Pool
							WHERE trainno = ".$_SESSION['trainno']."
							GROUP BY trainno";
				$results = pg_query($query);
				$coachmap = pg_fetch_array($results);
				$lower =0;
				if($_SESSION['chtype']=='A'){
					$lower += 0;
					$upper = $lower + $coachmap['nfac'];
				}else if($_SESSION['chtype']=='B'){
					$lower +=$coachmap['nfac'];
					$upper = $lower + $coachmap['nsac'];
				}else if($_SESSION['chtype']=='C'){
					$lower +=$coachmap['nfac']+$coachmap['nsac'];
					$upper = $lower + $coachmap['ntac'];
				}else if($_SESSION['chtype']=='SL'){
					$lower +=$coachmap['nfac']+$coachmap['nsac']+$coachmap['ntac'];
					$upper = $lower + $coachmap['nsleep'];
				}else if($_SESSION['chtype']=='CC'){
					$lower +=$coachmap['nfac']+$coachmap['nsac']+$coachmap['ntac']+$coachmap['nsleep'];
					$upper = $lower + $coachmap['ncc'];
				}else if($_SESSION['chtype']=='G'){
					$lower +=$coachmap['nfac']+$coachmap['nsac']+$coachmap['ntac']+$coachmap['nsleep']+$coachmap['ncc'];
					$upper = $lower + $coachmap['ngen'];
				}
				$lower *= SEATSPERCOACH;
				$upper *= SEATSPERCOACH;
				$query = 	"SELECT seatno1, seatno2
							FROM seatavlb_".$_SESSION['srcdate']."
							WHERE seatno1>=".$lower."
								AND seatno2<=".$upper."
								AND seatno1 <= seatno2
								AND trainno = ".$_SESSION['trainno']."
							LIMIT 1";
				$results = pg_query($query);
				$result = pg_fetch_array($results);
				$seatassigned = $result['seatno1'];
				if($result['seatno1']<$result['seatno2']){
					$query = 	"UPDATE seatavlb_".str_replace('-', '', $_SESSION['srcdate'])."
								SET seatno1 = seatno1 + 1
								WHERE seatno1 = ".$result['seatno1']."
									AND trainno = '".$_SESSION['trainno']."'";
					pg_query($query);
				}else{
					$query = 	"DELETE FROM seatavlb_".str_replace('-', '', $_SESSION['srcdate'])."
								WHERE seatno1 = ".$result['seatno1']."
									AND trainno = '".$_SESSION['trainno']."'";
					pg_query($query);
				}
				$query = "INSERT INTO trip_".str_replace('-', '', $_SESSION['srcdate'])." VALUES(
							DEFAULT,
							$pnr, '".
							$_SESSION['trainno']."',
							'N', '".
							$_SESSION['poolno']."','".
							$_SESSION['quota']."','
							RAC', '".
							$_SESSION['chtype']."', ".
							$seatassigned."', '".
							$_POST['passname'.$i]."', '".
							$_POST['passgender'.$i]."', ".
							$_POST['passage'.$i].")";
				pg_query($query);
				$ticket .= "<tr><td>".$_POST['passname'.$i]."</td><td>".$_POST['passage'.$i]."</td><td>".$_POST['passgender'.$i]."</td><td>RAC ".$seatassigned."</td></tr>";
			}else if($wlseats!=0){
				//WL RESERVATION -------------------------------
				$query = "UPDATE stats_".str_replace('-', '', $_SESSION['srcdate'])."
							SET nseats = nseats -1
							WHERE trainno = '".$_SESSION['trainno']."'
								AND chtype = '".$_SESSION['chtype']."'
								AND pool = '".$_SESSION['poolno']."'
								AND quota = '".$_SESSION['quota']."'
								AND status = 'WL'";
				pg_query($query);
				
				$query = "INSERT INTO trip_".str_replace('-', '', $_SESSION['srcdate'])." VALUES(
							DEFAULT,
							$pnr, '".
							$_SESSION['trainno']."',
							'N', '".
							$_SESSION['poolno']."','".
							$_SESSION['quota']."','
							WL', '".
							$_SESSION['chtype']."', 
							0, '".
							$_POST['passname'.$i]."', '".
							$_POST['passgender'.$i]."', ".
							$_POST['passage'.$i].")";
				pg_query($query);
				$ticket .= "<tr><td>".$_POST['passname'.$i]."</td><td>".$_POST['passage'.$i]."</td><td>".$_POST['passgender'.$i]."</td><td>WL 0000</td></tr>";
			}else{
				$string .= "<tr><td>".$_POST['passname'.$i]."</td><td>".$_POST['passage'.$i]."</td><td>".$_POST['passgender'.$i]."</td><td>REGRET</td></tr>";
			}
		}
	}
	echo $ticket;
?>
