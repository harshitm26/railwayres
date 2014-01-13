<?php
	require 'guard.php';
	require 'dblink.php';
	ini_set('display_errors',1);
	error_reporting(E_ALL | E_STRICT); 

	$pnr = $_POST['pnrnum'];
	$found = false;
	for($i=1; $i<=26; $i ++){
		$query = "SELECT * 
					FROM tkt_$i
					WHERE pnr = $pnr";

		$results = pg_query($query);
		$ticket = pg_fetch_array($results);
		if($ticket){
			$found = true;
			break;
		}
	}
	if(!$found){
		echo "PNR not found";
		exit();
	}
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
	$string="<table id='tickettable'><tr><th>Ticket</th></tr><tr><td><table>";
	$query = "SELECT * 
				FROM trip_$srcdate
				WHERE pnr = $pnr";
	$passengers = pg_query($query);
	$string .= "
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
		</tr></table></td></tr>";
	
	$string .= "<tr><td><table id='ticketpassengertable' ><tr><th>Name</th><th>Age</th><th>Gender</th><th>Seat</th></tr>";
	while($passenger= pg_fetch_array($passengers)){
		$string .= "<tr>
					<td>".$passenger['passname']."</td>
					<td>".$passenger['passage']."</td>
					<td>".$passenger['passgender']."</td>";
		if($passenger['cancelled']=='Y'){
			$string .= "<td>CAN</td>";
		}else if($passenger['status']=='CNF'){
			$string .= "<td>".$passenger['status']." ".$passenger['coach']." ".$passenger['seat']."</td>";
		}else if($passenger['status']=='RAC'){
			$query = "SELECT nseats, maxseats
						FROM stats_$srcdate
						WHERE trainno=".$passenger['trainno']."
							AND pool=".$passenger['pool']."
							AND chtype='".$passenger['coach']."'
							AND quota='".$passenger['quota']."'
						";
			
			$result=pg_fetch_array(pg_query($query));
			$string .= "<td>".$passenger['status']." ".($result['maxseats']-$result['nseats'])." / ".$passenger['coach']." ".$passenger['seat']."</td>";
		}else if($passenger['status']=='WL'){
			$query = "SELECT nseats, maxseats
						FROM stats_$srcdate
						WHERE trainno=".$passenger['trainno']."
							AND pool=".$passenger['pool']."
							AND chtype='".$passenger['coach']."'
							AND quota='".$passenger['quota']."'
						";
			
			$result=pg_fetch_array(pg_query($query));
			$string .= "<td>".$passenger['status']." ".$passenger['coach']." ".($result['maxseats']-$result['nseats'])."</td>";
		}
		$string .= "</tr>";
	}
	$string .= "</table></td></tr></table>";
	echo $string;
?>
