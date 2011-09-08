/* ============== AJAX =================================== */
function GetXmlHttpObject()
{ 
var objXMLHttp=null;
if (window.XMLHttpRequest)
{
objXMLHttp=new XMLHttpRequest();
}
else if (window.ActiveXObject)
{
objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP");
}
return objXMLHttp;
} 

function stateChanged() 
{ 
if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
{ 
document.getElementById("ajaxMsg").innerHTML="Submission Successful.";
} 
} 

var xmlHttp;

function showArticles(start) {

xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
{
alert ("Browser does not support HTTP Request");
return false;
} 
var url="/display_document_info.php";
url=url+"?start="+start;
url=url+"&sid="+Math.random();
xmlHttp.onreadystatechange=stateChanged;
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
} 

function doc_counter(the_loc) {

  if (the_loc.search.indexOf('u=')!=-1) {
	parts=the_loc.href.split('u=');
  	u_part=parts[1].split('&')[0];
  } 
  else { //if (loc.charAt(loc.length-1)!="/") {
	dirs=the_loc.href.split('/');
	u_part=dirs[dirs.length-1];
	u_part=u_part.split('?')[0].split('.')[0];
  }
	
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return false;
	} 
	var url="/scripts/doc_counter.php";
	url=url+"?u="+u_part;
	//url=url+"&sid="+Math.random();
	//xmlHttp.onreadystatechange=stateChanged;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
} 

/* ====================== Mouseover/Animation ======================= */
function anim2(imgObj,url) {
	imgObj.src=url;
}

function anim(name,type) {

	if (type==0)
		document.images[name].src="/images/"+name+".gif";
	if (type==1)
		document.images[name].src="/images/"+name+"_over.gif";
	if (type==2)
		document.images[name].src="/images/"+name+"_down.gif";
}

/* ================= For Player Form: Checks All or None ======== */

function checkAll(cbox,formObj) {
	var i=0;
	if (cbox.checked==true)
		cbox.checked==false;
	else
		cbox.checked==true;
	while (formObj.elements[i]!=null) {
		formObj.elements[i].checked=cbox.checked;
		i++;
	}
}
/* ================== For forms: Checks if Enter key is pressed ========== */

function checkEvent(formObj){
     var key = -1 ;
     var shift ;

     key = event.keyCode ;
     shift = event.shiftKey ;

     if (!shift && key == 13)
     {
          formObj.submit() ;
     }
}
/* ================= To show/hide a block of text ==================*/
function show(block) {
	theBlock=document.getElementById(block);
//	if (theBlock!=null) {
		if (theBlock.style.display=="none") {
			theBlock.style.display="block";
		}
		else {
			theBlock.style.display="none";
		}
//	}
}
/* ================ SetCookie ==============================*/
function setCookie()
{
     the_date = new Date("December 31, 2023");

     the_cookie_date = the_date.toGMTString();
//alert(the_cookie_date);
     the_login = document.forms['loginForm'].flogname.value;

    var the_cookie = "loginCookie=" + escape("loginName:"+the_login)+";expires="+the_cookie_date+";path=/;domain=fridayniteparty.com;";
//alert(the_cookie);
    document.cookie = the_cookie;

}
    var the_cookie = document.cookie;

    var the_cookie = unescape(the_cookie);
//alert(the_cookie);
    var broken_cookie = the_cookie.split(":");
//alert(broken_cookie[1]);
 //   the_name = broken_cookie[1];
//	lname=the_name.split(';')[0];
if (document.forms['loginForm']!=null) {
	document.forms['loginForm'].flogname.value=lname;
	document.forms['loginForm'].fpassword.focus();
}
//alert(the_name+",length:"+the_name.length);
//alert(the_name);
/* ============== */
function emailSelectCheck(emailObj,inputObj) {
	if (inputObj.value.indexOf(emailObj.innerHTML)!=-1) {
		emailObj.style.fontWeight='bold';
	}
	else {
		emailObj.style.fontWeight='normal';
	}
}
/* ================ */
function bolden(type,prefix,count) {
	
	for (i=0;i<count;i++) {
		document.getElementById(prefix+i).style.fontWeight=type;
	}
}
/*====================================================*/

function showPic(pic,imageTitle,description)
{
picWin=window.open("about:blank","","height=100,width=150,scrollbars=no,left=50,top=50");
with (picWin.document) {
writeln("<html><head><title>...Loading...</title><style>body{margin:0px;background:"+document.body.bgColor+"} a{text-decoration:none; font-family:Verdana;}</style>");
writeln('<sc'+'ript>');
writeln('var isNN,isIE;');writeln('if (parseInt(navigator.appVersion.charAt(0))>=4){');
writeln('isNN=(navigator.appName=="Netscape")?1:0;');writeln('isIE=(navigator.appName.indexOf("Microsoft")!=-1)?1:0;}');
writeln('function reSizeToImage(){');writeln('if (isIE){');writeln('window.resizeTo(100,100);');
writeln('width=100-(document.body.clientWidth-document.images[0].width)+50;');
writeln('height=200-(document.body.clientHeight-document.images[0].height)+50;');
writeln('window.resizeTo(width,height);}');writeln('if (isNN){');       
writeln('window.innerWidth=document.images["randPic"].width+50;');writeln('window.innerHeight=document.images["randPic"].height+90;}}');
writeln('function doTitle(){document.title="'+imageTitle+'";}');writeln('</sc'+'ript>');
writeln("</head><body class=frame scroll='no' onload='reSizeToImage();doTitle();self.focus()' onblur='close()'><table width=100% height=100%><tr><td style='vertical-align:middle'><center><span style='font-size:24;font-family:Monotype Corsiva;color:white'>"+imageTitle+"</span><img src="+pic+" name=randPic style='display:block'><p style='color:silver'>"+description+"<br><a href=javascript:window.close()>close</a></center></td></tr></table>");
writeln("</body></html>");
close();
}
}
/*
	var mediaItems = document.getElementsByTagName("td");
	for (var i=0; i<mediaItems.length; i++) {
		if(mediaItems[i].className == "media") {
			mediaItems[i].onmouseover=function() { this.bgColor = "silver";}
			mediaItems[i].onmouseout=function() { this.bgColor = "white";}
		}
	}
*/
/*==========================encode/decode==============================*/
// This code was written by Tyler Akins and has been placed in the
// public domain.  It would be nice if you left this header intact.
// Base64 code from Tyler Akins -- http://rumkin.com

var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";

function encode64(input) {
   var output = "";
   var chr1, chr2, chr3;
   var enc1, enc2, enc3, enc4;
   var i = 0;

   do {
      chr1 = input.charCodeAt(i++);
      chr2 = input.charCodeAt(i++);
      chr3 = input.charCodeAt(i++);

      enc1 = chr1 >> 2;
      enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
      enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
      enc4 = chr3 & 63;

      if (isNaN(chr2)) {
         enc3 = enc4 = 64;
      } else if (isNaN(chr3)) {
         enc4 = 64;
      }

      output = output + keyStr.charAt(enc1) + keyStr.charAt(enc2) + 
         keyStr.charAt(enc3) + keyStr.charAt(enc4);
   } while (i < input.length);
   
   return output;
}

function decode64(input) {
   var output = "";
   var chr1, chr2, chr3;
   var enc1, enc2, enc3, enc4;
   var i = 0;

   // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
   input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

   do {
      enc1 = keyStr.indexOf(input.charAt(i++));
      enc2 = keyStr.indexOf(input.charAt(i++));
      enc3 = keyStr.indexOf(input.charAt(i++));
      enc4 = keyStr.indexOf(input.charAt(i++));

      chr1 = (enc1 << 2) | (enc2 >> 4);
      chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
      chr3 = ((enc3 & 3) << 6) | enc4;

      output = output + String.fromCharCode(chr1);

      if (enc3 != 64) {
         output = output + String.fromCharCode(chr2);
      }
      if (enc4 != 64) {
         output = output + String.fromCharCode(chr3);
      }
   } while (i < input.length);

   return output;
}
/*=================== Confirming Button Click ===================== */
function confirmClick(message,href) {
	if (confirm(message))
 	{	
		location.href=href;
	} 
	else 
	{
		return false;
	}
}