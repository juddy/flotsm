<?

/*
	flotsm beta card v.0.2


    Based on ImageMagick class example
	written by: daniel@bokko.nl

	(c)1999 - 2003 All copyrights by: Daniël Eiland

	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.

	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details.
	(http://www.gnu.org/licenses/lgpl.txt)
*/

	$filename = '';


	/*
		Sets the target dir, make sure this directory is writeble by the httpd user
	*/

	$targetdir = './_process';



	if(isset($_FILES['image']) && $_FILES['image']['size'] > 0) {

		include('imagick/flotsm_class.php');

		$imObj = new ImageMagick($_FILES['image']);

		if(isset($_POST['verbose']) && $_POST['verbose'] == 1)
			$imObj -> setVerbose(TRUE);

		$imObj -> setTargetdir($targetdir);

		if(isset($_POST['resize']) && $_POST['resize'] == 1)
			$imObj -> Resize($_POST['resize_x'],$_POST['resize_y'], $_POST['resize_method']);

		if(isset($_POST['crop']) && $_POST['crop'] == 1)
			$imObj -> Crop($_POST['crop_x'],$_POST['crop_y'],$_POST['crop_method']);

		if(isset($_POST['square']) && $_POST['square'] == 1)
			$imObj -> Square($_POST['square_method']);

		if(isset($_POST['monochrome']) && $_POST['monochrome'] == 1)
			$imObj -> Monochrome();

		if(isset($_POST['negative']) && $_POST['negative'] == 1)
			$imObj -> Negative();

		if(isset($_POST['flip']) && $_POST['flip'] == 1)
			$imObj -> Flip($_POST['flip_method']);

		if(isset($_POST['dither']) && $_POST['dither'] == 1)
			$imObj -> Dither();

		if(isset($_POST['frame']) && $_POST['frame'] == 1)
			$imObj -> Frame($_POST['frame_width'], $_POST['frame_color']);

		if(isset($_POST['rotate']) && $_POST['rotate'] == 1)
			$imObj -> Rotate($_POST['rotate_angle'], $_POST['rotate_bgcolor']);

		if(isset($_POST['blur']) && $_POST['blur'] == 1)
			$imObj -> Blur($_POST['blur_radius'],$_POST['blur_sigma']);

		$imObj -> Convert($_POST['convert']);
		$filename = $imObj -> Save();
		$imObj -> CleanUp();

	}
?>
<html>
<head>
<title>flotsm beta card v.0.1</title>
<style>
<!--
	body {
		font-family: Verdana, Arial, Helvetica;
		color: #000000;
		font-size: 10pt;
	}
	td {
		font-size: 10pt;
	}
	td.main {
		font-size: 10pt;
		background-color: #494949;
	}
-->
</style>
</head>
<body bgcolor="#51647e">
Beta!  

Your card will appear below..
<center>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
<table cellspacing="0" cellpadding="0"><tr><td bgcolor="#000000">
<table cellspacing="1" cellpadding="2">
 <tr>
  <td class="main"><b>Select image:</b></td>
  <td class="main" colspan="4"><input type="file" name="image"></td>
 </tr>
  <tr>
  <td class="main"><b>Caption:</b></td>
  <td class="main" colspan="4"><input type="text" name="caption"></td>
 </tr>
 <tr>
  <td class="main"><b>Debug mode:</b></td>
  <td class="main" colspan="4"><input type="checkbox" name="verbose" value="1"<?=!isset($_POST['monochrome'])?' CHECKED':($_POST['monochrome']==1?' CHECKED':'')?>></td>
 </tr>
 <tr>
  <td class="main" colspan="5"><BR>Select options:</td>
 </tr>
 <tr>
  <td class="main"><b>Resize:</b></td>
  <td class="main"><input type="checkbox" name="resize" value="1"<?=isset($_POST['resize'])&&$_POST['resize']==1?' CHECKED':''?>></td>
  <td class="main">X-size: <input type="text" size="4" name="resize_x" value="<?=isset($_POST['resize_x'])?$_POST['resize_x']:'320'?>"></td>
  <td class="main">Y-size: <input type="text" size="4" name="resize_y" value="<?=isset($_POST['resize_y'])?$_POST['resize_y']:'240'?>"></td>
  <td class="main">Method: <select name="resize_method"><option<?=isset($_POST['resize_method'])&&$_POST['resize_method']=='keep_aspect'?' selected':''?>>keep_aspect</option><option<?=isset($_POST['resize_method'])&&$_POST['resize_method']=='fit'?' selected':''?>>fit</option></td>
 </tr>
 <tr>
  <td class="main"><b>Crop:</b></td>
  <td class="main"><input type="checkbox" name="crop" value="1"<?=isset($_POST['crop'])&&$_POST['crop']==1?' CHECKED':''?>></td>
  <td class="main">X-size: <input type="text" size="4" name="crop_x" value="<?=isset($_POST['crop_x'])?$_POST['crop_x']:'200'?>"></td>
  <td class="main">Y-size: <input type="text" size="4" name="crop_y" value="<?=isset($_POST['crop_y'])?$_POST['crop_y']:'300'?>"></td>
  <td class="main">Method: <select name="crop_method"><option<?=isset($_POST['crop_method'])&&$_POST['crop_method']=='center'?' selected':''?>>center</option><option<?=isset($_POST['crop_method'])&&$_POST['crop_method']=='left'?' selected':''?>>left</option><option<?=isset($_POST['crop_method'])&&$_POST['crop_method']=='right'?' selected':''?>>right</option></select></td>
 </tr>
 <tr>
  <td class="main"><b>Square:</b></td>
  <td class="main"><input type="checkbox" name="square" value="1"<?=isset($_POST['square'])&&$_POST['square']==1?' CHECKED':''?>></td>
  <td class="main" colspan="3">Method: <select name="square_method"><option<?=isset($_POST['square_method'])&&$_POST['square_method']=='center'?' selected':''?>>center</option><option<?=isset($_POST['square_method'])&&$_POST['square_method']=='left'?' selected':''?>>left</option><option<?=isset($_POST['square_method'])&&$_POST['square_method']=='right'?' selected':''?>>right</option></select></td>
 </tr>
 <tr>
  <td class="main"><b>Monochrome:</b></td>
  <td class="main" colspan="4"><input type="checkbox" name="monochrome" value="1"<?=isset($_POST['monochrome'])&&$_POST['monochrome']==1?' CHECKED':''?>></td>
 </tr>
 <tr>
  <td class="main"><b>Rotate:</b></td>
  <td class="main"><input type="checkbox" name="rotate" value="1"<?=isset($_POST['rotate'])&&$_POST['rotate']==1?' CHECKED':''?>></td>
  <td class="main">Angle: <input type="text" size="3" name="rotate_angle" value="<?=isset($_POST['rotate_angle'])?$_POST['rotate_angle']:'30'?>"></td>
  <td class="main" colspan="2">Matte background color: #<input type="text" size="7" maxlength="6" name="rotate_bgcolor" value="<?=isset($_POST['rotate_bgcolor'])?$_POST['rotate_bgcolor']:'FFA200'?>"><li><a href="" onclick="window.open('http://flotsm/flash/color_picker.swf','colorpicker','width=300,height=300,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,copyhistory=yes,resizable=yes')">colors</a></li></td>
 </tr>
  <tr>
  <td class="main"><b>Encapsulate file:</b></td>
  <td class="main" colspan="4"><input type="file" name="encapsulation"></td>
 </tr>
 <tr>
  <td class="main"><b>Generate:</b></td>
  <td class="main" colspan="4"><select name="convert"><option<?=isset($_POST['convert'])&&$_POST['convert']=='jpg'?' selected':''?>>jpg</option><option<?=isset($_POST['convert'])&&$_POST['convert']=='gif'?' selected':''?>>gif</option><option<?=isset($_POST['convert'])&&$_POST['convert']=='png'?' selected':''?>>png</option></td>
 </tr>
 <tr>
  <td class="main" colspan="5">&nbsp;</td>
 </tr>
 <tr>
  <td class="main">&nbsp;</td>
  <td class="main" colspan="4"><input type="submit" value="Generate Token!"></td>
 </tr>
</table>
</td></tr></table>
</form>
<?
	if(isset($filename) && $filename != '') {
		?>
		<tr>
			<br><br><img src="_process/<?=$filename?>" border="0">
		</tr>	
		<?
	}
?>
</center>
</body>
</html>
