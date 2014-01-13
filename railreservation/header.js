var temp;
temp=new XMLHttpRequest();

function handwave(msg){
	//~ document.getElementById('handwave').innerHTML += msg;
}
function clearsidediv(){
	handwave('clearsidediv()');
	document.getElementById('sidediv1').style.display='none';
	document.getElementById('sidediv2').style.display='none';
	document.getElementById('sidediv3').style.display='none';
	document.getElementById('sidediv4').style.display='none';
}
function clearmaindiv(){
	handwave('clearmaindiv()');
	document.getElementById('maindiv1').innerHTML="";
	document.getElementById('maindiv2').style.display = 'none';
	document.getElementById('maindiv3').innerHTML="";
	document.getElementById('maindiv4').innerHTML="";
	document.getElementById('maindiv5').innerHTML="";
	
}
function clear(){
		
		clearsidediv();
		clearmaindiv();
	}

function showsidediv(id){
	clear();
	document.getElementById(id).style.display="inline";
}

function showBookForm(){
	handwave('showBookForm() start');
	document.getElementById('maindiv2').style.display='inline';
	document.getElementById('bookForm').reset();
}	
window.onload=clear;
document.onload=clear;

function header1(id,text,x){
	var c=document.getElementById(id);
	document.getElementById(id).width=x-20;
	var ctx=c.getContext("2d");
	ctx.fillStyle="#33AD5C";
	//ctx.linestyle="#33AD5C"
	ctx.fillRect(0,0,x,100);
	ctx.font="40px New York";
	ctx.fillStyle="Black";
	ctx.textAlign="center";
	ctx.textBaseline="middle";
	ctx.fillText(text,x/2-10,50);
}

function updateprofile(x,username){
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			document.getElementById('myid4').innerHTML="";
			document.getElementById('myid3').innerHTML="";
			document.getElementById(x).innerHTML="\
		<form method='post' action='' align='center'>\
			<table align='center'>\
				<tr>\
					<td align='left'>\
						User Name:\
					</td>\
					<td align='right'>\
						"+username+"\
					</td>\
				</tr>\
				<tr>\
					<td align='left'>\
						First Name:\
					</td>\
					<td align='right'>\
						<input class='input' type='text' id='upfname' name='fname'/>\
					</td>\
				</tr>\
				<tr>\
					<td align='left'>\
						Last Name:\
					</td>\
					<td align='right'>\
						<input class='input' type='text' id='uplname' name='lname'/>\
					</td>\
				</tr>\
				<tr>\
					<td align='left'>\
						Email Address:\
					</td>\
					<td align='right'>\
						<input class='input' type='text' id='upemail' name='email'/>\
					</td>\
				</tr>\
				<tr>\
					<td align='left'>\
						Phone Number:\
					</td>\
					<td align='right'>\
						<input class='input' type='text' id='upphone'  name='phone'/>\
					</td>\
				</tr>\
				<tr>\
					<td align='left'>\
						DOB:\
					</td>\
					<td align='right'>\
						<input class='input' type='text' id='updob' name='dob'/>\
					</td>\
				</tr>\
				<tr>\
					<td>\
						</br></br>\
					</td>\
				</tr>\
			</table>\
		</form>\
		<input type='submit' value='Submit' onclick='unseen_updateprofile(&quot;"+username+"&quot;)' style='float:bottom'/>\
		";
		}
	}
	temp.open("GET","",true);
	temp.send();
}
function unseen_updateprofile(username){
	//~ alert("unseenupdateprofile.php?ouname="+username+"&upfname="+document.getElementById("upfname").value+"&uplname="+document.getElementById("uplname").value+"&upphone="+document.getElementById("upphone").value+"&upemail="+document.getElementById("upemail").value+"&updob="+document.getElementById("updob").value);
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			document.getElementById("myid3").innerHTML=temp.responseText;
		}
	}
	temp.open("GET","unseenupdateprofile.php?ouname="+username+"&upfname="+document.getElementById("upfname").value+"&uplname="+document.getElementById("uplname").value+"&upphone="+document.getElementById("upphone").value+"&upemail="+document.getElementById("upemail").value+"&updob="+document.getElementById("updob").value,true);
	temp.send();
}

function unseen_changepassword(username){
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			document.getElementById("myid3").innerHTML=temp.responseText;
		}
	}
	//~ temp.open("GET","",true);
	temp.open("GET","unseenchangepassword.php?username="+username+"&opswd="+document.getElementById("opswd").value+"&npswd="+document.getElementById("npswd").value+"&npswd1="+document.getElementById("npswd1").value,true);
	temp.send();
}


//~ "&quot;,&quot;"+username+
function profile(x,y,username){
		temp.onreadystatechange=function(){
			if(temp.readyState==4 && temp.status==200){
				document.getElementById(y).innerHTML="";
				document.getElementById('myid4').innerHTML="";
				document.getElementById("myid3").innerHTML="";
				document.getElementById(x).innerHTML="<table>\
				<tr>\
					<td>\
						<input class='sbuttonclass'type='button' value='Change Profile' onclick='updateprofile(&quot;"+y+"&quot;,&quot;"+username+"&quot;);'></br></br>	\
					</td>\
				</tr>\
				<tr>\
					<td>\
						<input class='sbuttonclass'type='button' value='Change Password' onclick='changepassword(&quot;"+y+"&quot;,&quot;"+username+"&quot;);'></br></br>	\
					</td>\
				</tr>\
			</table>";
			}
		}
		temp.open("GET","",true);
		temp.send();
}

function transaction(x,y){
		temp.onreadystatechange=function(){
			if(temp.readyState==4 && temp.status==200){
				document.getElementById(y).innerHTML="";
				document.getElementById('myid4').innerHTML="";
				document.getElementById('myid3').innerHTML="";
				document.getElementById(x).innerHTML="<table>\
				<tr>\
					<td>\
						<input class='sbuttonclass'type='button' value='Booked History' onclick='updateprofile(&quot;"+y+"&quot;);'></br></br>	\
					</td>\
				</tr>\
				<tr>\
				<tr>\
					<td>\
						<input class='sbuttonclass'type='button' value='Cancel E-Ticket/Refund' onclick='updateprofile(&quot;"+y+"&quot;);'></br></br>	\
					</td>\
				</tr>\
				<tr>\
				<tr>\
					<td>\
						<input class='sbuttonclass'type='button' value='Cancelled History' onclick='updateprofile(&quot;"+y+"&quot;);'></br></br>	\
					</td>\
				</tr>\
				<tr>\
					<td>\
						<input class='sbuttonclass'type='button' value='File TDR' onclick='changepassword(&quot;"+y+"&quot;);'></br></br>	\
					</td>\
				</tr>\
				<tr>\
					<td>\
						<input class='sbuttonclass'type='button' value='TDR History' onclick='changepassword(&quot;"+y+"&quot;);'></br></br>	\
					</td>\
				</tr>\
			</table>\
			</form>"
			}
		}
		temp.open("GET","",true);
		temp.send();
}

function pnrstatus(){
	handwave('pnrstatus() start');

	var params= "pnrnum="+document.getElementById('pnrnum').value;

	
	temp.open("POST", "pnrStatus.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", params.length);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			document.getElementById('maindiv5').innerHTML=temp.responseText;
		}
	}
	temp.send(params);
	handwave('pnrStatus() end');
}


function unseen_service(a,z){
	//~ alert('unseen_Service'+' '+a+' '+z);
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			document.getElementById(a).innerHTML=z;
		}
	}
	temp.open("GET","",true);
	temp.send();
}


function traindetail(tclass,trainno, tdate,trainname){
	var temp1;
	temp1=new XMLHttpRequest();
	temp1.onreadystatechange=function(){
		if(temp1.readyState==4 && temp1.status==200){
			document.getElementById('myid4').innerHTML=temp1.responseText;
		}
	}
	var objectquota=document.getElementById('quota');
	var quota= objectquota.options[objectquota.selectedIndex].text;
	temp1.open("GET","traindetail.php?from="+document.getElementById('from').value+"&to="+document.getElementById('to').value+"&class="+tclass+"&date="+tdate+"&trainno="+trainno+"&quota="+quota+"&trainname="+trainname,true);
	temp1.send();
}


function getfare(from,to,tclass,trainno,quota,date){
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			document.getElementById('myid5').innerHTML=temp.responseText;
		}
	}
	temp.open("GET","getfare.php?from="+from+"&to="+to+"&class="+tclass+"&trainno="+trainno+"&quota="+quota+"&date="+date,true);
	temp.send();
}

function getAvailability(trainno,trtype, srcdate,poolno, quota, chtype, fromdtdep, findate, todtarr, fromstno, tostno){
	handwave('getAvailability() start');

	var params= "quota="+quota
					+"&trainno="+trainno
					+"&trtype="+trtype
					+"&srcdate="+srcdate
					+"&poolno=" + poolno
					+"&chtype=" + chtype
					+"&fromdtdep=" + fromdtdep
					+"&findate=" + findate
					+"&todtarr=" + todtarr
					+"&fromstno="+fromstno
					+"&tostno="+tostno;
	
	
	temp.open("POST", "getAvailability.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", params.length);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
				document.getElementById('maindiv2').style.display = 'none';
			document.getElementById('maindiv5').innerHTML="";
			document.getElementById('maindiv1').innerHTML=temp.responseText;
		}
	}
	temp.send(params);
	handwave('getAvailability() end');
}


function findTrains(){
	handwave('findTrains() start');

	var objectquota=document.getElementById('quota');
	var quota= objectquota.options[objectquota.selectedIndex].value;
	
	
	var params= "quota="+quota+"&from="+document.getElementById('from').value
	+"&to=" + document.getElementById('to').value
	+"&jrndate=" + document.getElementById('date').value;
	
	
	temp.open("POST", "findTrains.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", params.length);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			clearmaindiv();
			document.getElementById('maindiv3').innerHTML=temp.responseText;
		}
	}
	temp.send(params);
	handwave('findTrains() end');
}



function service(x,y){
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			clear();
			document.getElementById('sidediv1').style.display = 'inline';
			document.getElementById('sidediv1').innerHTML = temp.responseText;
		}
	}
	temp.open("GET","serviceform.php",true);
	temp.send();
}

function confirm(){
	handwave('confirm() start');

	var objectquota=document.getElementById('quota');
	var quota= objectquota.options[objectquota.selectedIndex].value;

	var params= "passname1="+document.getElementById('passname1').value
				+"&passage1="+document.getElementById('passage1').value
				+"&passgender1="+document.getElementById('passgender1').options[document.getElementById('passgender1').selectedIndex].value
				+"&passname2="+document.getElementById('passname2').value
				+"&passage2="+document.getElementById('passage2').value
				+"&passgender2="+document.getElementById('passgender2').options[document.getElementById('passgender2').selectedIndex].value
				+"&passname3="+document.getElementById('passname3').value
				+"&passage3="+document.getElementById('passage3').value
				+"&passgender3="+document.getElementById('passgender3').options[document.getElementById('passgender3').selectedIndex].value
				+"&passname4="+document.getElementById('passname4').value
				+"&passage4="+document.getElementById('passage4').value
				+"&passgender4="+document.getElementById('passgender4').options[document.getElementById('passgender4').selectedIndex].value
				+"&passname5="+document.getElementById('passname5').value
				+"&passage5="+document.getElementById('passage5').value
				+"&passgender5="+document.getElementById('passgender5').options[document.getElementById('passgender5').selectedIndex].value
				+"&passname6="+document.getElementById('passname6').value
				+"&passage6="+document.getElementById('passage6').value
				+"&passgender6="+document.getElementById('passgender6').options[document.getElementById('passgender6').selectedIndex].value;
				
				
	temp.open("POST", "book.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", params.length);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			document.getElementById('maindiv5').innerHTML=temp.responseText;
		}
	}
	temp.send(params);
	handwave('confirm() end');
}

function history(){
	handwave('history() start');
	var params = '';
	temp.open("POST", "history.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", params.length);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			clearmaindiv();
			document.getElementById('maindiv1').innerHTML=temp.responseText;
		}
	}
	temp.send();
}

function showticket(pnr){
	handwave('showticket() start');

	var params= "pnrnum="+pnr;

	
	temp.open("POST", "pnrStatus.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", params.length);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			document.getElementById('maindiv5').innerHTML=temp.responseText;
		}
	}
	temp.send(params);
	handwave('pnrStatus() end');	
}

function goforcancellation(pnr){
	handwave('goforcancellation() start');

	var params= "pnrnum="+pnr;

	
	temp.open("POST", "goforcancellation.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", params.length);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			clearmaindiv();
			document.getElementById('maindiv4').innerHTML=temp.responseText;
		}
	}
	temp.send(params);
	handwave('goforcancellation() end');	
}	

function cancel(pnr, srcdate, passnum){
	handwave('cancel() start');

	var params= "pnrnum="+pnr
				+"&srcdate="+srcdate;
	for(var i=1; i<=passnum; i++)
	{
		if(document.getElementById("cancel"+i).checked==true){
			params += "&cancel"+i+"=1";
		}
		else{
			params += "&cancel"+i+"=0";
		}
	}			
	temp.open("POST", "cancel.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", params.length);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			clearmaindiv();
			document.getElementById('maindiv4').innerHTML=temp.responseText;
		}
	}
	temp.send(params);
	handwave('cancel() end');	
}	



function filetdr(){
	handwave('filetdr() start');
	var params = '';
	temp.open("POST", "tdr.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", params.length);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			clearmaindiv();
			document.getElementById('maindiv1').innerHTML=temp.responseText;
		}
	}
	temp.send();
}

function tdrfiling(pnr){
	handwave('tdrfiling() start');

	var params= "pnrnum="+pnr;
	
	temp.open("POST", "tdrfiling.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", params.length);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			clearmaindiv();
			document.getElementById('maindiv4').innerHTML=temp.responseText;
		}
	}
	temp.send(params);
	handwave('tdrfiling() end');	
}	

function tdrfinal(pnr, srcdate,passnum){
	handwave('tdrfinal() start');

	var params= "pnrnum="+pnr
				+"&srcdate="+srcdate;
	params += "&reason="+document.getElementById('reason').value;
	for(var i=1; i<=passnum; i++)
	{
		if(document.getElementById("tdr"+i).checked==true){
			params += "&tdr"+i+"=1";
		}
		else{
			params += "&tdr"+i+"=0";
		}
	}			

	temp.open("POST", "tdrfinal.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", params.length);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			clearmaindiv();
			document.getElementById('maindiv4').innerHTML=temp.responseText;
		}
	}
	temp.send(params);
	handwave('tdrfinal() end');	
}	

function changepassword(){
	handwave('changepassword() start');
	temp.open("POST", "changepassword.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", 0);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			clearmaindiv();
			document.getElementById('maindiv4').innerHTML=temp.responseText;
		}
	}
	temp.send();

	handwave('changepassword() end');
}

function changepasswordfinal(){
	
	var params = "oldpwd="+document.getElementById('oldpwd').value
					+"&newpwd="+document.getElementById('newpwd').value;
	
	handwave('changepasswordfinal() start');
	temp.open("POST", "changepasswordfinal.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", params.length);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			clearmaindiv();
			document.getElementById('maindiv4').innerHTML=temp.responseText;
		}
	}
	temp.send(params);

	handwave('changepasswordfinal() end');
}

function updateprofile(){
	handwave('updateprofile() start');
	temp.open("POST", "updateprofile.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", 0);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			clearmaindiv();
			document.getElementById('maindiv4').innerHTML=temp.responseText;
		}
	}
	temp.send();

	handwave('updateprofile() end');
}

function updateprofilefinal(){
	
	var params = "fname="+document.getElementById('fname').value
					+"&lname="+document.getElementById('lname').value
					+"&contact="+document.getElementById('contact').value
					+"&email="+document.getElementById('email').value
					+"&dob="+document.getElementById('dob').value
					+"&gender="+document.getElementById('gender').value;
	
	handwave('updateprofilefinal() start');
	temp.open("POST", "updateprofilefinal.php", true);
	temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	temp.setRequestHeader("Content-length", params.length);
	temp.setRequestHeader("Connection", "close");
	temp.onreadystatechange=function(){
		if(temp.readyState==4 && temp.status==200){
			clearmaindiv();
			document.getElementById('maindiv4').innerHTML=temp.responseText;
		}
	}
	temp.send(params);

	handwave('updateprofilefinal() end');
}
