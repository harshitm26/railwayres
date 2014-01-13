	<?php
	define('PAST', 20); //seconds
	define('FUTURE', 40); //seconds
	define('SEATSPERCOACH', 100);
	require 'dblink.php';
	//Ticket table
	$today = date('Ymd');
	$query ='';
	for($d = date('Ymd', strtotime($today. ' - '.PAST.' days')); 
		$d <= date('Ymd', strtotime($today. ' + '.FUTURE.' days'));
		$d = date('Ymd', strtotime($d. ' + 1 day'))){
		
		$query .= "DROP TABLE IF EXISTS seatavlb_$d ;\n
			CREATE TABLE seatavlb_$d(
			trainno integer NOT NULL REFERENCES Train(trainno) ON DELETE CASCADE,
			seatno1 smallint NOT NULL CONSTRAINT valid_seatno1 CHECK(seatno1 > 0), 
			seatno2 smallint NOT NULL CONSTRAINT valid_seatno2 CHECK(seatno2 >= seatno1),
			PRIMARY KEY (trainno, seatno1)
		);\n";
		
		echo "tkt_$d created ";
		$query .= "DROP TABLE IF EXISTS trip_$d;\n
					CREATE TABLE trip_$d(
						resid integer NOT NULL DEFAULT NEXTVAL('residcntr'),
						pnr INTEGER NOT NULL, 
						trainno integer NOT NULL REFERENCES Train(trainno) ON DELETE CASCADE,
						cancelled YN NOT NULL, 
						pool smallint NOT NULL, 
						quota QUOTA NOT NULL , 
						status STATUS NOT NULL, 
						coach COACHTYPE NOT NULL, 
						seat smallint CONSTRAINT valid_seat CHECK (seat BETWEEN 1 AND 72),
						passname_first varchar(20) NOT NULL, 
						passname_last varchar(20), 
						passgender GENDER NOT NULL, 
						passage smallint CONSTRAINT valid_age CHECK (passage > 0), 
						PRIMARY KEY (resid) 
					);\n";
		echo "trip_$d created";
		
		
		$query .= "DROP TABLE IF EXISTS stats_$d;\n
					CREATE TABLE stats_$d(
								trainno integer NOT NULL REFERENCES Train(trainno) ON DELETE RESTRICT, 
								pool smallint NOT NULL, 
								chtype COACHTYPE NOT NULL, 
								quota QUOTA NOT NULL, 
								status STATUS NOT NULL, 
								nseats smallint NOT NULL, 
								PRIMARY KEY (trainno, pool, chtype, quota, status)
					);\n";

		echo "stats_$d created<br/>\n";	

	}
	print "$query\n";
	pg_query($query);
	
	$day = date("D", strtotime($today));
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
		
	for($d = date('Ymd', strtotime($today)); 
		$d <= date('Ymd', strtotime($today. ' + '.FUTURE.' days'));
		$d = date('Ymd', strtotime($d. ' + 1 day')), $dayn = ($dayn +1)%7){
			$query = "SELECT * FROM Train NATURAL JOIN Train_days WHERE days=$dayn";
			print $query."<br/>\n";
			$trains = pg_query($query);
			$query = '';
			while($train = pg_fetch_array($trains)){
				$q = "SELECT * FROM Pool WHERE trainno='".$train['trainno']."'";
				//print $q."<br/>\n";
				$traincoaches = 0;
				$pools = pg_query($q);
				
				while($pool = pg_fetch_array($pools)){
					$poolcoaches = $pool['nsleep'] + $pool['ngen'] + $pool['nfac'] +
					$pool['nsac'] + $pool['ntac'] + $pool['ncc'];
					$traincoaches += $poolcoaches;
					$coachseats = 0;
					
					for($i =1; $i<=6; $i++){
						switch($i){
							case(1): $coach = 'G'; $coachseats = $pool['ngen']*SEATSPERCOACH; break;
							case(2): $coach = 'SL'; $coachseats = $pool['nsleep']*SEATSPERCOACH;  break;
							case(3): $coach = 'A'; $coachseats = $pool['nfac']*SEATSPERCOACH;  break;
							case(4): $coach = 'B'; $coachseats = $pool['nsac']*SEATSPERCOACH;  break;
							case(5): $coach = 'C'; $coachseats = $pool['ntac']*SEATSPERCOACH;  break;
							case(6): $coach = 'CC'; $coachseats = $pool['ncc']*SEATSPERCOACH;  break;
						}
						
						
						//For GEN quota
						$genseats = (100 - $train['pladiesquota'] - $train['ptatkalquota'])*$coachseats/100;
							//For CONF status
							$genconfseats = $genseats*(100-$train['pwlres']-$train['pracres'])/100;
							$query .= "INSERT INTO stats_$d VALUES('".$train['trainno']."', '".$pool['stno']."', '$coach', 'GN', 'CNF', $genconfseats);";
							
							//print $query."<br/>\n";
							//For WL status
							$genwlseats = $genseats*($train['pwlres'])/100;
							$query .= "INSERT INTO stats_$d VALUES('".$train['trainno']."', '".$pool['stno']."', '$coach', 'GN', 'WL', $genwlseats);";
							//print $query."<br/>\n";
							
							//For RAC status
							$genracseats = $genseats*($train['pracres'])/100;
							$query .= "INSERT INTO stats_$d VALUES('".$train['trainno']."', '".$pool['stno']."', '$coach', 'GN', 'RAC', $genracseats);";
							//print $query."<br/>\n";
							
						//For TAT quota
						$tatseats = ($train['ptatkalquota'])*$coachseats/100;
							//For CONF status
							$tatconfseats = $tatseats*(100-$train['pwlres']-$train['pracres'])/100;
							$query .= "INSERT INTO stats_$d VALUES('".$train['trainno']."', '".$pool['stno']."', '$coach', 'TKL', 'CNF', $tatconfseats);";
							//print $query."<br/>\n";
							
							//For WL status
							$tatwlseats = $tatseats*($train['pwlres'])/100;
							$query .= "INSERT INTO stats_$d VALUES('".$train['trainno']."', '".$pool['stno']."', '$coach', 'TKL', 'WL', $tatwlseats);";
							//print $query."<br/>\n";
							
							//For RAC status
							$tatracseats = $tatseats*($train['pracres'])/100;
							$query .= "INSERT INTO stats_$d VALUES('".$train['trainno']."', '".$pool['stno']."', '$coach', 'TKL', 'RAC', $tatracseats);";
							//print $query."<br/>\n";
							
						//For LAD quota
						$ladseats = ($train['pladiesquota'])*$coachseats/100;
							//For CONF status
							$ladconfseats = $ladseats*(100-$train['pwlres']-$train['pracres'])/100;
							$query .= "INSERT INTO stats_$d VALUES('".$train['trainno']."', '".$pool['stno']."', '$coach', 'LD', 'CNF', $ladconfseats);";
							//print $query."<br/>\n";
							
							//For WL status
							$ladwlseats = $ladseats*($train['pwlres'])/100;
							$query .= "INSERT INTO stats_$d VALUES('".$train['trainno']."', '".$pool['stno']."', '$coach', 'LD', 'WL', $ladwlseats);";
							//print $query."<br/>\n";
							
							//For RAC status
							$ladracseats = $ladseats*($train['pracres'])/100;
							$query .= "INSERT INTO stats_$d VALUES('".$train['trainno']."', '".$pool['stno']."', '$coach', 'LD', 'RAC', $ladracseats);";
							//print $query."<br/>\n";
					
					}
					
				}
				
			}
			pg_query($query);
			//print $query."<br/>\n";
		}
			

?>
	
