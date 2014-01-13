<?php
	require 'header.php';
	
	if(isset($_POST['uname'])){
		require 'dblink.php';
		$uname = strtolower($_POST['uname']);
		$num = ord($uname[0])-96;
		$query="insert into user_".$num." values('$uname','$_POST[pwd]','$_POST[phone]','$_POST[fname]','$_POST[lname]','$_POST[email]','$_POST[gender]','$_POST[dob]')";
		if(!pg_query($query)){
			print 'Registration unsuccessful';
		}else{
			print 'User registered successfully<br/>';
		}
	}
	
	
	
	
?>
<form method="post" action="register.php" align="center">
			
			<table align="center">
				<tr><td></td><td>
					<img src="captcha.php" /></td>
				</tr>
				<tr>
					<td>Enter the words shown</td>
					<td align="right"><input class="input" type="text" name="captcha" /> </td>
				</tr>
				
				<tr>
					<td align="left">
						First Name:
					</td>
					<td align="right">
						<input class="input" type="text" name="fname"/>
					</td>
				</tr>
				<tr>
					<td align="left">
						Last Name:
					</td>
					<td align="right">
						<input class="input" type="text" name="lname"/>
					</td>
				</tr>
				<tr>
					<td align="left">
						User Name:
					</td>
					<td align="right">
						<input class="input" type="text" name="uname"/>
					</td>
<!--
					<td>
						<button type="button" onclick="check_username()">Check Username</button>
					</td>
-->
				</tr>
				<tr>
					<td align="left">
						Email Address:
					</td>
					<td align="right">
						<input class="input" type="text" name="email"/>
					</td>
				</tr>
				<tr>
					<td align="left">
						Phone Number:
					</td>
					<td align="right">
						<input class="input" type="text" name="phone"/>
					</td>
				</tr>
				<tr>
					<td align="left">
						Password:
					</td>
					<td align="right">
						<input type="password" class="input" type="text" name="pwd"/>
					</td>
				</tr>
				<tr>
					<td align="left">
						DOB:
					</td>
					<td align="right">
						<input class="input" type="text" name="dob"/>
					</td>
				</tr>
					
				<tr>
					<td>
						Gender:
					</td>
					<td>
						<input type="radio" name="gender" value="Male"> Male</br>
					</td>
					<td>
						<input type="radio" name="gender" value="Female">Female</br>
					</td>
				</tr>
				<tr>
					<td></td><td align="center">
						<input type="submit" value="Submit" style="float:bottom"/>
					</td>
				</tr>
			</table>
			
		</form>

<?php
	  require 'footer.php';

?>
