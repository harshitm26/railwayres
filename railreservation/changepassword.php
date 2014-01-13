<?php
	require 'guard.php';
	require 'dblink.php';
	ini_set('display_errors',1);
	error_reporting(E_ALL | E_STRICT); 
	$string = "<form id = 'changepassword'><table>";
	$string .= "<tr><td>Old Password:</td><td><input type='password' id='oldpwd'></td></tr>";
	$string .= "<tr><td>New Password:</td><td><input type='password' id='newpwd'></td></tr>";
	$string .= "<tr><td align='center' colspan='2'><input type='button' value='Update Password' class='sbuttonclass' onclick='changepasswordfinal()'>";
	$string .= "</table></form>";
	echo $string;
?>
