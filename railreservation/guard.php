<?php
	ob_start();
	session_start();
	define('SEATSPERCOACH', 100);
if(empty($_SESSION['loggedin']) OR isset($_POST['logout'])){
	unset($_SESSION);
	session_destroy();
	header('Location: index.php');
	exit();
}
define('TIMEOUT', 6000);
if(isset($_SESSION['loggedin']) AND (time()-$_SESSION['loggedin']>TIMEOUT)){
	unset($_SESSION);
	session_destroy();
	print 'Please log in to continue';
	exit();
}
if(isset($_SESSION['loggedin'])) $_SESSION['loggedin']=time();
?>
