<?php
	require 'guard.php';
	require 'dblink.php';
	ini_set('display_errors',1);
	error_reporting(E_ALL | E_STRICT); 
	$username = strtolower($_SESSION['userid']);
	$num = ord($username[0])-96;
	
	
	$query = "SELECT *, CURRENT_TIMESTAMP as cur
				FROM tkt_$num
				WHERE userid = '$username'
			";
	
	$tickets = pg_query($query);
	$string = "<table id='historytable'>";
	$string .= "<tr><th colspan='2'>Ticket Booking History</th></tr>";
	while($ticket = pg_fetch_array($tickets)){
		$string .= "
			<tr>
				<td onclick='showticket(".$ticket['pnr'].")'><u>PNR: ".$ticket['pnr']."</u></td>";
		if($ticket['dtjour']>$ticket['cur']){
			$string.="<td onclick='goforcancellation(".$ticket['pnr'].")'><u>Go for cancellation</u></td>";
		}else{
			$string .= "<td></td>";
		}
		$string.="
			</tr>
			<tr>
				<td> Train no.:".$ticket['trainno']."</td>
				<td> DOJ: ".date('Y-m-d', strtotime($ticket['dtjour']))."</td>
			</tr>";
	}
	$string .= "</table>";
	echo $string;
?>
