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
					AND dtjour < current_timestamp
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
	
	$query = "SELECT resid 
				FROM tdrresid;
			";
	$results = pg_query($query);
	$filedresids = array();
	while($filedresid = pg_fetch_array($results)){
		$filedresids[] = $filedresid['resid'];	
	}		
	//~		TDR filing reasons:
	$reasons = array(	
					"Train Cancelled.",
					"Train Late More Than Three Hours and Passenger Not Travelled.",
					"Difference Of Fare In Case proper Coach Not Attached.",
					"AC Failure",
					"Travelled Without Proper ID Proof.",
					"Wrongly Charged BY TTE.",
					"Party Partially Travelled.",
					"Passenger Not Travelled.",
					"Train Diverted And Passenger Not Travelled.",
					"Train Diverted And Train Not Touching Boarding Station.",
					"Train Diverted And Train Not Touching Destination Station.",
					"Passenger Not Travelled As Reservation Provided In Lower Class.",
					"Passenger Not Travelled Due To Ticket In RAC After Chart Preparation.",
					"Train Terminated Short Of Destination.",
					"Party Partially Confired/Waitlisted And Waitlisted Passengers Did Not Travel.",
					"Party Partially Confired/Waitlisted And All Passengers Did Not Travel.",
					"Party Could Not Cancel Because Chart Prepared At Originating Or Previous Remote Location.",
					"Train Missed As Connecting Train Was Late.",
					"Change In Reservation Status From Confirmed To Waitlisted/Part Waitlisted/RAC After Chart Preparation.",
					"After Charting No Room Provided.",
					"Difference Of Fare As Passenger Travelled In Lower Class.",
					"Passenger Unable to Travel Due to Wrong Departure Time Updated in PRS System.",
					"Passenger Not Travelled Due To Coach Damage.",
					"Unable To Cancel Due To Error Message Received From PRS System.",
					"Fare Difference Due To Decrease In Fare Amount. "
				);
	$countreasons = count($reasons);
	
	
	$string = "<table id='tdrfilingtable'>";
	$string .= "
			<tr><th>File TDR</th></tr>
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
		</tr>";
	
	
	$string .= "<tr><td><form>
		<table><tr><th>Name</th><th>Age</th><th>Gender</th><th>Seat</th><th>File TDR?</th></tr>";
	$i=1;
	while($passenger= pg_fetch_array($passengers)){
		$string .= "<tr>
					<td>".$passenger['passname']."</td>
					<td>".$passenger['passage']."</td>
					<td>".$passenger['passgender']."</td>";
		if($passenger['cancelled']=='Y'){
			$string .= "<td>CAN</td>";
			$string .= "<td><input type='checkbox' id='tdr$i' style='visibility:hidden;'></td>";
		}else if(in_array($passenger['resid'],$filedresids)){
			$string .= "<td>".$passenger['status']." ".$passenger['coach']." ".$passenger['seat']."</td>";
			$string .= "<td>Already Filed<input type='checkbox' id='tdr$i' style='visibility:hidden;'></td>";
		}else if($passenger['status']=='CNF'){
			$string .= "<td>".$passenger['status']." ".$passenger['coach']." ".$passenger['seat']."</td>";
			$string .= "<td><input type='checkbox' id='tdr$i'></td>";
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
			$string .= "<td><input type='checkbox' id='tdr$i'></td>";
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
			$string .= "<td><input type='checkbox' id='tdr$i'></td>";
		}
		$string .= "</tr>";
		$i++;			
	}
	$passnum = $i-1;
	$string .= "</table></form></td></tr>";
	
	$string .= "<tr><td align='center'>";
	$string .= "Reason for TDR filing:";
	$string .= "</td></tr>";

	$string .= "<tr><td align='center'>";
	$string .= "<select style='width:300px' id='reason'>";
	for($i = 0; $i < $countreasons; $i++){
		$string .="<option value='$reasons[$i]'>$reasons[$i]</option>";
	}
	$string .= "</select>";
	$string .= "</td></tr>";

	$string .= "<tr><td align='center'><input type='button' class='sbuttonclass' onclick='tdrfinal($pnr, $srcdate,$passnum)' value='File TDR'></td></tr>";
	$string .= "</table>";
	echo $string;
?>
