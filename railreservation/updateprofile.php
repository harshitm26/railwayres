<?php
	require 'guard.php';
	require 'dblink.php';
	ini_set('display_errors',1);
	error_reporting(E_ALL | E_STRICT); 
	$username = strtolower($_SESSION['userid']);
	$num = ord($username[0])-96;
	
	$query = "SELECT * 
				FROM user_$num
					WHERE userid = '$username'
			";
	$result = pg_fetch_array(pg_query($query));
	
	$string = "<form id = 'updateprofile'><table>";	
	$string .= "<tr>
					<td align='left'>
						User Name:
					</td>
					<td align='left'>
						$result[0]
					</td>
				</tr>
				<tr>
					<td align='left'>First Name:</td>
					<td align='right'><input class='input' type='text' id='fname' value='$result[3]'></td>
				</tr>
				<tr>
					<td align='left'>Last Name:</td>
					<td align='right'><input class='input' type='text' id='lname' value='$result[4]'></td>
				</tr>
				<tr>
					<td align='left'>
						Email Address:
					</td>
					<td align='right'>
						<input class='input' type='text' id='email' value='$result[5]'>
					</td>
				</tr>
				<tr>
					<td align='left'>
						Phone Number:
					</td>
					<td align='right'>
						<input class='input' type='text' id='contact' value='$result[2]'>
					</td>
				</tr>
				<tr>
					<td align='left'>
						DOB:
					</td>
					<td align='right'>
						<input class='input' type='text' id='dob' value='$result[7]'>
					</td>
				</tr>
					
				<tr>
					<td>
						Gender:
					</td>
					<td>
						<input type='radio' id='gender' value='Male' checked> Male</br>
					</td>
					<td>	
						<input type='radio' id='gender' value='Female'>Female</br>
					</td>
				</tr>
			";
			
	$string .= "<tr><td align='center' colspan='2'><input type='button' value='Update Profile' class='sbuttonclass' onclick='updateprofilefinal()'>";
	$string .= "</table></form>";
	echo $string;
?>
