<?php
	require 'guard.php';
	require 'dblink.php';
	ini_set('display_errors',1);
	error_reporting(E_ALL | E_STRICT); 
	$username = strtolower($_SESSION['userid']);
	$num = ord($username[0])-96;
	$fname=$_POST['fname'];
	$lname=$_POST['lname'];
	$contact=$_POST['contact'];
	$dob=$_POST['dob'];
	$gender=$_POST['gender'];
	$email=$_POST['email'];
		
	$query = "UPDATE user_$num
				SET firstname = '$fname',
					lastname = '$lname',
					contact = '$contact',
					emailid = '$email',
					gender = '$gender',
					dob = '$dob'
					WHERE userid = '$username'
			";
	pg_query($query);
	echo "Profile Successfully Updated!";
?>
