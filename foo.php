<?php
$is_submitted = $HTTP_POST_VARS["submit"];
$file_name = $HTTP_POST_VARS["new_file"];

if(!isset($is_submitted)){
?>

<html>
<head>
</head>
<body bgcolor="#FFFFFF" text="#000000">
<form name="form1" method="post" action="<?php echo $PHP_SELF; ?>">
<input type="text" name="new_file">
<br>
<br>
<input type="submit" name="submit" value="Submit">
</form>
</body>
</html>

<?php
}
else{

$contents = <<<EOD

<HTML>
<HEAD>
</HEAD>
<BODY leftMargin=0 topMargin=0 link="#000080" vlink="#000080" alink="#000080" bgcolor="#CCFF99">
<table border="1" cellpadding="0" cellspacing="0" width="100%" height="100%" bordercolor="#000000" bgcolor="#CCFF99">
<tr>
<td width="100%" valign="top">

<div align="center">
<table border="0" cellpadding="3" width="400" background="../topmenu.gif" height="30">
<tr>
<td>
<p align="center"><font color="#FFFFFF" size="2" face="Arial"><b>General Pool</b></font></td>
</tr>
</table>
</div>

<div align="center">
<TABLE cellSpacing=0 cellPadding=0 border=1 width="440" bordercolor="#555576">
<TBODY>
<tr>
<TD bgColor=#000000 width="317">
<p align="left"><font color="#ffffff" face="Verdana" size="1"><b>Post
<a href="postnewtopic1.htm"><img border="0" src="postnew.gif" width="86" height="19"></a></b></font></p>
</TD>
<TD bgColor=#000000 width="123">
<p align="left"><font color="#ffffff" face="Verdana" size="1"><b>Poster</b></font></p>
</TD>
</tr>


EOD;

$file_pointer = fopen($file_name, "w");
$lock = flock($file_pointer, LOCK_EX);

if ($lock){
fputs($file_pointer, $contents);
flock($file_pointer, LOCK_UN);
}
fclose($file_pointer);


echo <<<EOD

<html>
<head>
<META HTTP-EQUIV="Refresh" Content="3; URL=$file_name">
</head>
<body>
Loading your page please wait...
</body>
</html>

EOD;
}
?>