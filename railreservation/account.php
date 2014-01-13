<?php
  require 'guard.php';
  ob_start();
  ini_set('display_errors',1);
  error_reporting(E_ALL | E_STRICT);  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>Railway ticket reservation system</title>
	<script src="header.js" type="text/javascript"></script>
	<script src="jquery.js" type="text/javascript"></script>
	<script type="text/javascript" src=	"dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
	<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen"></link>
	<link type="text/css" rel="stylesheet" href="account.css" media="screen"></link>
</head>
<body style="margin:0; background-color:rgb(250, 250, 250);font-family:Verdana,Arial,serif; font-size:60%" >
  <h1 style="margin:0; padding-top:1%; padding-bottom:1%; background-color:#33ad5c; text-align:center; color:white">
	<table style="margin-left:2%; margin-right:2%; width:96%">
	<tr><td colspan="2" style="text-align:center">Railway ticket reservation system</td></tr>
	<tr style="font-size:40%"><td style="text-align:center">A CS315 project by Jeetesh, Harshit, Vinit</td></tr>
	</table>
  </h1>
  <br/>
  <span id='handwave'></span>
	<table><tr><td>
		<table><tr><td>
			<input class="buttonclass" type="button" value="Services" onclick="showsidediv('sidediv1');">
			<div id="sidediv1">
				<form method='post' action='' align='center' >
					<table align='center'>
						<tr><td align='left'>From:</td>
							<td align='right'><input class='input' type='text' size='8' id='from' name='from'/></td>
						<tr><td align='left'>To:</td>
							<td align='right'><input class='input' type='text' size='8' id='to' name='to'/></td>
						</tr>
						<tr><td align='left'>Date:</td>
							<td align='right'><input class='input' type='text' size='8' id='date' name='date'/></td>
						</tr>
						<tr><td colspan='2' align='center'><input type='button' value='Calender' onclick= 'displayCalendar(document.forms[0].date	, &quot;yyyy/mm/dd &quot;,this)' style='width:150'></td>
						</tr>
						<tr><td align='left'>Quota:</td>
							<td align='right'><select name='users' id='quota' onchange='showUser(this.value)'>
								<option value='GN'>GN</option>
								<option value='TKL'>TKL</option>
								<option value='LD'>LD</option>
							</select></td>
						</tr>
						<tr><td colspan='2' align='center'><input type='button' value='Find Trains' id='mybutton' onclick='findTrains();' style='float:bottom'>
						</td></tr>
							
					</table>
				</form>
				
			</div>
		</td></tr>
		<tr><td>
			<input class="buttonclass" type="button" value="My Transactions" id="transaction" onclick="showsidediv('sidediv2');">
			<div id="sidediv2">
			<form><table align="center">
				<tr><td>
					<input class='sbuttonclass' type='button' value='Booking History' onclick='history();'>
				</td></tr>
				<tr><td>
					<input class='sbuttonclass'type='button' value='File TDR' onclick='filetdr();'>
				</td></tr>
			</table></form>
			</div>	
		</td></tr>
		<tr><td>
			<input class='buttonclass' type='button' value='Profile' onclick="showsidediv('sidediv3');">
			<div id="sidediv3" >
				<table align="center">
					<tr><td>
						<input class='sbuttonclass'type='button' value='Change Profile' onclick='updateprofile();'>
					</td></tr>
					<tr><td>
						<input class='sbuttonclass'type='button' value='Change Password' onclick='changepassword();'>
					</td></tr>
				</table>
			</div>
		</td></tr>
		<tr><td>
			<input class="buttonclass"type="button" value="PNR Status" onclick="showsidediv('sidediv4');"><br/>
			<div id="sidediv4">
				<table align="center">
					<tr><td><input class='input' type='text' size='8' id='pnrnum' name='pnrnum'></td></tr>
					<tr><td><input class='sbuttonclass'type='button' value='Check' onclick='pnrstatus();'></td></tr>
				</table>
			</div>	
		</td></tr>
		<tr><td><input class="buttonclass" type="button" value="Logout" onclick="window.location.href='./logout.php'"></td></tr>
		</table>
	</td><td>
		<table><tr><td>
				<div id="maindiv1" style="float:left;">
				</div>
			</td><td>
				<div id="maindiv2" style="float:right;">
					<form>
						<table id='booktable'>
						<tr><th colspan='3'>Enter Passenger information</th></tr>
						<tr><th>Passenger Name</th><th>Age</th><th>Gender</th></tr>
						<?php
							for($i=1; $i<=6; $i++){
								print "<tr><td><input style='height:50%' type='text' size='16' id='passname$i'></td>
								<td><input type='text' size='2' id='passage$i'></td>
								<td><select id='passgender$i'>
									<option value='Male'>Male</option>
									<option value='Female'>Female</option></select>
								</td></tr>";
							}
						?>
						<tr><td colspan='3' align='center'>
							<input type='button' class='sbuttonclass' onclick='confirm()' value='Confirm'>
						</td></tr>
						</table>
					</form>
				</div>
			</td><td><div id="maindiv5"></div>
		</td></tr></table>
		<div id="maindiv4">
		</div>
		<div id="maindiv3">
		</div>
	</td></tr>
	</table>
	</body>

</html>
<!--onchange='showUser(this.value)'-->
