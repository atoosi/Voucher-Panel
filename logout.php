<html> 
<head> 
<script language="javascript" type="text/javascript"> 
function closeWindow() { 
//uncomment to open a new window and close this parent window without warning 
//var newwin=window.open("popUp.htm",'popup',''); 
if(navigator.appName=="Microsoft Internet Explorer") { 
this.focus();self.opener = this;self.close(); } 
else { window.open('','_parent',''); window.close(); } 
} 
</script> 
</head> 
<body> 
<a href="javascript:closeWindow();">Close Window</a> 
</body> 
</html>