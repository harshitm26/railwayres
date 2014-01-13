<?php
	$con = pg_connect("host=localhost dbname=railres user=postgres password=jeeteshm");
		if(!$con){
	print '<style="text-align:center"></style>Problem in connecting to database.<br/>Please try again after sometime.<br/></style>';
	exit();			
		}
//die(mysql_errno() . ": " . mysql_error() . "<br>");
?>
