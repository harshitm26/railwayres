<?php
	require 'header.php';

  $message='';
  if(!isset($_POST['logout'])){
	  if(!empty($_SESSION['loggedin'])){
		ob_end_clean();
		header('Location: account.php');
		exit();
	  }
	  $username='';
	  $password='';
	  $res='';
	  if(isset($_POST['register'])){
		ob_end_clean();
		header('Location: register.php');
		exit();
	  }	  
	  else if(isset($_POST['username'])) $username = trim(substr($_POST['username'], 0, 255));
	  if(isset($_POST['password'])) $password = trim(substr($_POST['password'], 0, 255));
	  if(!isset($_POST['captcha'])) $message='';
	  else if(empty($_POST['captcha'])) $message='You did not answer the captcha test!';
	  else if(!isset($_SESSION['jetmango'])) $message='Please try again';
	  else if($_POST['captcha']!=$_SESSION['jetmango']) $message='You did not pass the captcha test!';
	  else if(!isset($_POST['username'])) $message='';
	  else if(empty($username)) $message='Only usernames containing 1-255 alphanumeric characters allowed';
	  else if(!isset($_POST['password'])) $message='';
	  else if(empty($password)) $message='Only usernames containing 1-255 alphanumeric characters allowed';
	  else if(isset($_POST['login'])){
		require 'dblink.php';
		$username = pg_escape_string(strtolower($username));
		$password = pg_escape_string($password);
		$num = ord($username[0])-96;
		$query = 'SELECT userid FROM user_'.$num.' WHERE userid = \''.$username.'\' AND pwd = \''.$password.'\'';
		//print $query;
		if(!($res=pg_query($query))){
			$message = 'Problem with database. Please try again';
		}else{
			$details = @pg_fetch_array($res);
				//print $details['userid'];
				if(empty($details['userid'])){
					$message='No such user-password pair';
				}else{
						$_SESSION['loggedin']=time();
						$_SESSION['userid']=$details['userid'];				
						header('Location: account.php');
						exit();
				}
		}
	  }
	}
	else{
		session_start();
		unset($_SESSION);
		session_destroy();
		header('Location: index.php');
	}  
?>
  <form action="index.php" method="post">
  <table style="margin-left:30%; margin-right:30%; width:40%; background-color:rgb(237, 239, 244); text-align:center; color:rgb(51,51,51); font-size:130%">
  <tr><td colspan="2" >Username</td></tr>
  <tr><td colspan="2" ><input type="text" maxlength="255" name="username" size="20" /></td>
  <tr><td colspan="2" >Password</td></tr>
  <tr><td colspan="2" ><input type="password" maxlength="255" name="password" size="20"/></td></tr>
  </tr></table>
  <br/><br/><br/><br/>
  <table style="margin-left:30%; margin-right:30%; width:40%; background-color:rgb(237, 239, 244); text-align:center; color:rgb(51,51,51); font-size:130%">
  <tr><td colspan="2" >Please type the letters shown.</td></tr>
  <tr><td colspan="2" ><br/></td></tr>
  <tr><td colspan="2" ><img src="./captcha.php"/></td></tr>
  <tr><td colspan="2" ><br/></td></tr>
  <tr><td colspan="2" ><input type="text" maxlength="6" name="captcha" size="12" /></td></tr>
  <tr><td><input type="submit" name="login" value="Log in"/></td><td><input type="submit" name="register" value="Register"/></td></tr>
<?php
  if(!empty($message)) print '<tr><td colspan="2" style="font-weight:bold; color:red">'.$message.'</td></tr>';
?>
  </form></table></body></html>
<?php
	require 'footer.php';
?>
