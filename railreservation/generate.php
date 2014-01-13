	<?php
	define('PAST', 5); //seconds
	define('FUTURE', 10); //seconds
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
						seat smallint,
						passname varchar(50) NOT NULL, 
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
								maxseats smallint NOT NULL,
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
			$avlb = '';
			while($train = pg_fetch_array($trains)){
				$q = "SELECT * FROM Pool WHERE trainno='".$train['trainno']."'";
				//print $q."<br/>\n";
				$trainfaccoaches = 0;
				$trainsaccoaches = 0;
				$traintaccoaches = 0;
				$trainsleepcoaches = 0;
				$traincccoaches = 0;
				$traingencoaches = 0;
				$pools = pg_query($q);
				
				while($pool = pg_fetch_array($pools)){
					$poolcoaches = $pool['nsleep'] + $pool['ngen'] + $pool['nfac'] +
					$pool['nsac'] + $pool['ntac'] + $pool['ncc'];
					$trainfaccoaches += $pool['nfac'];
					$trainsaccoaches += $pool['nsac'];
					$traintaccoaches += $pool['ntac'];
					$trainsleepcoaches += $pool['nsleep'];
					$traincccoaches += $pool['ncc'];
					$traingencoaches += $pool['ngen'];
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
							$genconfseats = $genseats*(100-$train['pracres'])/100;
							$query .= $train['trainno'].",".$pool['stno'].",$coach,GN,CNF,$genconfseats,$genconfseats\n";
							
							//print $query."<br/>\n";
							//For WL status
							$genwlseats = $genseats*($train['pwlres'])/100;
							$query .= $train['trainno'].",".$pool['stno'].",$coach,GN,WL,$genwlseats,$genwlseats\n";
							//print $query."<br/>\n";
							
							//For RAC status
							$genracseats = $genseats*($train['pracres'])/100;
							$query .= $train['trainno'].",".$pool['stno'].",$coach,GN,RAC,$genracseats,$genracseats\n";
							//print $query."<br/>\n";
							
						//For TAT quota
						$tatseats = ($train['ptatkalquota'])*$coachseats/100;
							//For CONF status
							$tatconfseats = $tatseats*(100-$train['pracres'])/100;
							$query .= $train['trainno'].",".$pool['stno'].",$coach,TKL,CNF,$tatconfseats,$tatconfseats\n";
							//print $query."<br/>\n";
							
							//For WL status
							$tatwlseats = $tatseats*($train['pwlres'])/100;
							$query .= $train['trainno'].",".$pool['stno'].",$coach,TKL,WL,$tatwlseats,$tatwlseats\n";
							//print $query."<br/>\n";
							
							//For RAC status
							$tatracseats = $tatseats*($train['pracres'])/100;
							$query .= $train['trainno'].",".$pool['stno'].",$coach,TKL,RAC,$tatracseats,$tatracseats\n";
							//print $query."<br/>\n";
							
						//For LAD quota
						$ladseats = ($train['pladiesquota'])*$coachseats/100;
							//For CONF status
							$ladconfseats = $ladseats*(100-$train['pracres'])/100;
							$query .= $train['trainno'].",".$pool['stno'].",$coach,LD,CNF,$ladconfseats,$ladconfseats\n";
							//print $query."<br/>\n";
							
							//For WL status
							$ladwlseats = $ladseats*($train['pwlres'])/100;
							$query .= $train['trainno'].",".$pool['stno'].",$coach,LD,WL,$ladwlseats,$ladwlseats\n";
							//print $query."<br/>\n";
							
							//For RAC status
							$ladracseats = $ladseats*($train['pracres'])/100;
							$query .= $train['trainno'].",".$pool['stno'].",$coach,LD,RAC,$ladracseats,$ladracseats\n";
							//print $query."<br/>\n";
					
					}
					
				}
				$last = 0;
				$avlb .= $train['trainno'].",".(1+$last).",".($last+$trainfaccoaches*SEATSPERCOACH)."\n";
				$last += $trainfaccoaches*SEATSPERCOACH;
				$avlb .= $train['trainno'].",".(1+$last).",".($last+$trainsaccoaches*SEATSPERCOACH)."\n";
				$last += $trainsaccoaches*SEATSPERCOACH;
				$avlb .= $train['trainno'].",".(1+$last).",".($last+$traintaccoaches*SEATSPERCOACH)."\n";
				$last += $traintaccoaches*SEATSPERCOACH;
				$avlb .= $train['trainno'].",".(1+$last).",".($last+$trainsleepcoaches*SEATSPERCOACH)."\n";
				$last += $trainsleepcoaches*SEATSPERCOACH;
				$avlb .= $train['trainno'].",".(1+$last).",".($last+$traincccoaches*SEATSPERCOACH)."\n";
				$last += $traincccoaches*SEATSPERCOACH;
				$avlb .= $train['trainno'].",".(1+$last).",".($last+$traingencoaches*SEATSPERCOACH)."\n";
				$last += $traingencoaches*SEATSPERCOACH;
				
			}
			//pg_query($query);
			//print $query."<br/>\n";
			$f = fopen("./stats/stats_$d", "w+");
			fwrite($f, $query);
			fclose($f);
			$f = fopen("./seatavlb/seatavlb_$d", "w+");
			fwrite($f, $avlb);
			fclose($f);
			
			$q = "COPY stats_$d FROM '/home/vinit/WEBSITES/railways/railres/stats/stats_$d' CSV";
			print ($q."<br/>\n");
			pg_query($q);
			$q = "COPY seatavlb_$d FROM '/home/vinit/WEBSITES/railways/railres/seatavlb/seatavlb_$d' CSV";
			print ($q."<br/>\n");
			pg_query($q);
		}
			

?>
	
