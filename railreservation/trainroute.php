<html>
	<head>
		<style>
			.myTable1 { background-color:#E0FFDE;border-collapse:collapse;color:#000;font-size:18px; }
			.myTable1 th { background-color:#33AD5C;color:black; }
			.myTable1 td, .myTable1 th { padding:5px;border:0; }
			.myTable1 td { border-bottom:1px dotted #BDB76B; }
		</style>
		<script src="header.js"></script>	
	</head>
	<body onload="header1('myCanvas','Railway Ticket Reservation System',screen.width);header1('traininfo','Train Route',screen.width/3);";>
	<div align="center">
		<canvas id="myCanvas" width="100"height="100" style="border:1px solid #d3d3d3;">
		</canvas>
	</div></br>
	<div align="center">
		<canvas id="traininfo" width="100"height="100" style="border:1px solid #d3d3d3;">
		</canvas>
	</div></br></br>
	 <?php
		if(isset($_GET["trainno"])){
			$trainno=$_GET["trainno"];
			require 'dblink.php';
			$query=
			"select * from 
			(
				select stno, stcode, dtarr, dtdep, dayoffset from 
					(select * from Train where trainno=".$trainno.") as temp1
					natural join 
					Stand	
			)as withoutstnames
			natural join 
			Station;
			";	
			$result=pg_query($con, $query);
			if(!pg_num_rows($result)){
				echo "<p align=center>No route available for Train Number: ".$trainno."</p>";
			}
			else{
				echo"<table class='myTable1'  align=\"center\">";
				echo"<tr>
						<th>Station Name</th>
						<th>Station Code</th>
						<th>Station Number</th>
						<th>Arrival</th>
						<th>Departure</th>
						<th>Day Offset</th>
					</tr>";
				while ($row = pg_fetch_array($result)) {
					echo"<tr>";
					echo "<td>".$row[5]."</td>";
					echo "<td>".$row[0]."</td>";
					echo "<td>".$row[1]."</td>";
					echo "<td>".$row[2]."</td>";
					echo "<td>".$row[3]."</td>";
					echo "<td>".$row[4]."</td>";
					echo"</tr>";
				}
				echo"</table>";
			}
		}
	?>
	</body>
</html>
	
