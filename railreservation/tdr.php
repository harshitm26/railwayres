<?php
	require 'guard.php';
	require 'dblink.php';
	ini_set('display_errors',1);
	error_reporting(E_ALL | E_STRICT); 
	$username = strtolower($_SESSION['userid']);
	$num = ord($username[0])-96;
	
	
	$query = "SELECT *
				FROM tkt_$num
				WHERE userid = '$username'";
	
	$tickets = pg_query($query);
	$string = "<table id='filetdrtable'>";
	$today = date("Ymd");
	while($ticket = pg_fetch_array($tickets)){
		$string .= "
			<tr>
			<td onclick='showticket(".$ticket['pnr'].")'><u>".$ticket['pnr']."</u></td>
			";
			
		if(date('Ymd', strtotime($ticket['dtjour'])) <= $today){	
			$string .= "
				<td onclick='tdrfiling(".$ticket['pnr'].")'><u>Go for TDR filing</u></td>";
		}else{
			$string .= "<td>Cannot file TDR for future tickets</td>";
		}
		$string .="
			</tr>
			<tr>
				<td> Train no.:".$ticket['trainno']."</td>
				<td> DOJ: ".date('Y-m-d', strtotime($ticket['dtjour']))."</td>
			</tr>";
	}
	$string .= "</table>";
	echo $string;
?>
