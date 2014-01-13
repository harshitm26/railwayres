<?php
	require 'guard.php';
	require 'dblink.php';
	ini_set('display_errors',1);
	error_reporting(E_ALL | E_STRICT); 
	$oldpwd = $_POST['oldpwd'];
	$newpwd = $_POST['newpwd'];
	$username = strtolower($_SESSION['userid']);
	$num = ord($username[0])-96;
	$query = "SELECT pwd 
				FROM user_$num
					WHERE userid='$username'
			";
	$results = pg_query($query);
	$result = pg_fetch_array($results);
	if($oldpwd != $result['pwd'] || $newpwd == ''){
		echo "Enter correct values! Password Unchanged.";
	}else{
		$query = "UPDATE user_$num 
					SET pwd = '$newpwd'
						WHERE userid = '$username'
				";
		pg_query($query);
		echo "Password Successfully Updated!";		
	}
?>
