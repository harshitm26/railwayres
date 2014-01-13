<?php
	require 'guard.php';
	require 'dblink.php';
	ini_set('display_errors',1);
	error_reporting(E_ALL | E_STRICT); 
	$srcdate = $_POST['srcdate'];
	$pnr = $_POST['pnrnum'];
	$query = "SELECT *
				FROM trip_$srcdate
				WHERE pnr=$pnr
				ORDER BY resid ASC
			"; 
	
	$passengers = pg_query($query);
	
	$i=0;
	while($passenger = pg_fetch_array($passengers)){
		$i=$i+1;
		if($_POST["cancel$i"]==0){
			echo "NOT Cancelling $i<br/>";
			continue;
		}
		$trainno = $passenger['trainno'];
		$pool = $passenger['pool'];
		$quota = $passenger['quota'];
		$resid = $passenger['resid'];
		$coach = $passenger['coach'];
		$seat = $passenger['seat'];
		$status=$passenger['status'];
		$query = "UPDATE trip_$srcdate
					SET cancelled='Y', seat='0'
					WHERE resid=$resid;
				";
		pg_query($query);
		
		if($status=='CNF'){//If A is CNF
			$query = "SELECT *
						FROM trip_$srcdate
						WHERE cancelled='N'
							AND trainno=$trainno
							AND pool = $pool
							AND quota = '$quota'
							AND coach = '$coach'
							AND status = 'RAC'
						ORDER BY resid ASC LIMIT 1
					";
			$ummeedvaar = pg_fetch_array(pg_query($query));
			if(!$ummeedvaar){//If there is no B
				$query = 	"UPDATE stats_$srcdate
								SET nseats = nseats +1
								WHERE trainno = $trainno
									AND pool = $pool
									AND quota = '$quota'
									AND chtype = '$coach'
									AND status='CNF';
							INSERT INTO seatavlb_$srcdate VALUES(
								$trainno,
								$seat,
								$seat
							);
						";
				pg_query($query);
			}else{//If there is B
				$seat = $ummeedvar['seat'];
				$query = "UPDATE trip_$srcdate
							SET seat=$seat, status='CNF'
							WHERE resid=".$ummeedvaar['resid']."
						";//set B to CNF
				pg_query($query);
				$query = "SELECT *
							FROM trip_$srcdate
							WHERE cancelled='N'
								AND trainno=$trainno
								AND pool = $pool
								AND quota = '$quota'
								AND coach = '$coach'
								AND status = 'WL'
							ORDER BY resid ASC LIMIT 1
						";
				$aglaummeedvaar = pg_fetch_array(pg_query($query));
				if(!$aglaummeedvaar){//if there is no C
					$query = 	"UPDATE stats_$srcdate
									SET nseats = nseats +1
									WHERE trainno = $trainno
										AND pool = $pool
										AND quota = '$quota'
										AND chtype = '$coach'
										AND status='RAC';
								INSERT INTO seatavlb_$srcdate VALUES(
									$trainno,
									$seat,
									$seat
								);
							";
					pg_query($query);
				}else{//If there is C
					$query = "UPDATE trip_$srcdate
								SET seat=$seat, status='RAC'
								WHERE resid=".$aglaummeedvaar['resid']."
							";//set C to RAC
					pg_query($query);
					$query = 	"UPDATE stats_$srcdate
									SET nseats = nseats +1
									WHERE trainno = $trainno
										AND pool = $pool
										AND quota = '$quota'
										AND chtype = '$coach'
										AND status='WL';
									INSERT INTO seatavlb_$srcdate VALUES(
										$trainno,
										$seat,
										$seat
									);
								";
					pg_query($query);
				}
			}
		}else if($status=='RAC'){//if A is RAC
			$query = "SELECT *
						FROM trip_$srcdate
						WHERE cancelled='N'
							AND trainno=$trainno
							AND pool = $pool
							AND quota = '$quota'
							AND coach = '$coach'
							AND status = 'WL'
						ORDER BY resid ASC LIMIT 1
					";
			$ummeedvaar = pg_fetch_array(pg_query($query));
			if(!$ummeedvaar){//If there is no B
				$query = 	"UPDATE stats_$srcdate
								SET nseats = nseats +1
								WHERE trainno = $trainno
									AND pool = $pool
									AND quota = '$quota'
									AND chtype = '$coach'
									AND status='RAC';
							INSERT INTO seatavlb_$srcdate VALUES(
								$trainno,
								$seat,
								$seat
							);
						";
				pg_query($query);
			}else{//If there is B
				$query = "UPDATE trip_$srcdate
							SET seat=$seat, status='RAC'
							WHERE resid=".$ummeedvaar['resid']."
						";//set B to RAC
				pg_query($query);
				//there is no C
				$query = 	"UPDATE stats_$srcdate
								SET nseats = nseats +1
								WHERE trainno = $trainno
									AND pool = $pool
									AND quota = '$quota'
									AND chtype = '$coach'
									AND status='WL';
							INSERT INTO seatavlb_$srcdate VALUES(
								$trainno,
								$seat,
								$seat
							);
						";
				pg_query($query);
			}
		}else if($status=='WL'){
			$query = 	"UPDATE stats_$srcdate
							SET nseats = nseats +1
							WHERE trainno = $trainno
								AND pool = $pool
								AND quota = '$quota'
								AND chtype = '$coach'
								AND status='WL';
							INSERT INTO seatavlb_$srcdate VALUES(
								$trainno,
								$seat,
								$seat
							);
						";
			pg_query($query);
		}
	}
	echo 'Cancelled successfully';
