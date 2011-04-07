<?

/*
	ImageMagick class example
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

		include('imagemagick_class.php');

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
<title>ImageMagick class 1.0 example</title>
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
		background-color: #FFA200;
	}
-->
</style>
</head>
<body bgcolor="#FFA200">
<center>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" enctype="multipart/form-data">
<table cellspacing="0" cellpadding="0"><tr><td bgcolor="#FFFFFF">
<table cellspacing="1" cellpadding="2">
 <tr>
  <td class="main"><b>Select image:</b></td>
  <td class="main" colspan="4"><input type="file" name="image"></td>
 </tr>
 <tr>
  <td class="main"><b>Verbose mode:</b></td>
  <td class="main" colspan="4"><input type="checkbox" name="verbose" value="1"<?=!isset($_POST['monochrome'])?' CHECKED':($_POST['monochrome']==1?' CHECKED':'')?>></td>
 </tr>
 <tr>
  <td class="main" colspan="5"><BR>Select options:</td>
 </tr>
 <tr>
  <td class="main"><b>Resize:</b></td>
  <td class="main"><input type="checkbox" name="resize" value="1"<?=isset($_POST['resize'])&&$_POST['resize']==1?' CHECKED':''?>></td>
  <td class="main">X-size: <input type="text" size="4" name="resize_x" value="<?=isset($_POST['resize_x'])?$_POST['resize_x']:'640'?>"></td>
  <td class="main">Y-size: <input type="text" size="4" name="resize_y" value="<?=isset($_POST['resize_y'])?$_POST['resize_y']:'480'?>"></td>
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
  <td class="main"><b>Flip:</b></td>
  <td class="main"><input type="checkbox" name="flip" value="1"<?=isset($_POST['flip'])&&$_POST['flip']==1?' CHECKED':''?>></td>
  <td class="main" colspan="3">Method: <select name="flip_method"><option<?=isset($_POST['flip_method'])&&$_POST['flip_method']=='horizontal'?' selected':''?>>horizontal</option><option<?=isset($_POST['flip_method'])&&$_POST['flip_method']=='vertical'?' selected':''?>>vertical</option></td>
 </tr>
 <tr>
  <td class="main"><b>Dither:</b></td>
  <td class="main" colspan="4"><input type="checkbox" name="dither" value="1"<?=isset($_POST['dither'])&&$_POST['dither']==1?' CHECKED':''?>></td>
 </tr>
 <tr>
  <td class="main"><b>Frame:</b></td>
  <td class="main"><input type="checkbox" name="frame" value="1"<?=isset($_POST['frame'])&&$_POST['frame']==1?' CHECKED':''?>></td>
  <td class="main">Framewidth: <input type="text" size="3" name="frame_width" value="<?=isset($_POST['frame_width'])?$_POST['frame_width']:'5'?>"></td>
  <td class="main" colspan="2">Framecolor: #<input type="text" size="7" maxlength="6" name="frame_color" value="<?=isset($_POST['frame_color'])?$_POST['frame_color']:'FF0000'?>"></td>
 </tr>
 <tr>
  <td class="main"><b>Rotate:</b></td>
  <td class="main"><input type="checkbox" name="rotate" value="1"<?=isset($_POST['rotate'])&&$_POST['rotate']==1?' CHECKED':''?>></td>
  <td class="main">Angle: <input type="text" size="3" name="rotate_angle" value="<?=isset($_POST['rotate_angle'])?$_POST['rotate_angle']:'30'?>"></td>
  <td class="main" colspan="2">Background color: #<input type="text" size="7" maxlength="6" name="rotate_bgcolor" value="<?=isset($_POST['rotate_bgcolor'])?$_POST['rotate_bgcolor']:'FFA200'?>"></td>
 </tr>
 <tr>
  <td class="main"><b>Blur:</b></td>
  <td class="main"><input type="checkbox" name="blur" value="1"<?=isset($_POST['blur'])&&$_POST['blur']==1?' CHECKED':''?>></td>
  <td class="main">Radius: <input type="text" size="3" name="blur_radius" value="<?=isset($_POST['blur_radius'])?$_POST['blur_radius']:'5'?>"></td>
  <td class="main" colspan="2">Sigma: <input type="text" size="3" name="blur_sigma" value="<?=isset($_POST['blur_sigma'])?$_POST['blur_sigma']:'2'?>"></td>
 </tr>
 <tr>
  <td class="main"><b>Convert to:</b></td>
  <td class="main" colspan="4"><select name="convert"><option<?=isset($_POST['convert'])&&$_POST['convert']=='jpg'?' selected':''?>>jpg</option><option<?=isset($_POST['convert'])&&$_POST['convert']=='gif'?' selected':''?>>gif</option><option<?=isset($_POST['convert'])&&$_POST['convert']=='png'?' selected':''?>>png</option></td>
 </tr>
 <tr>
  <td class="main" colspan="5">&nbsp;</td>
 </tr>
 <tr>
  <td class="main">&nbsp;</td>
  <td class="main" colspan="4"><input type="submit" value="convert"></td>
 </tr>
</table>
</td></tr></table>
</form>
<?
	if(isset($filename) && $filename != '') {
		?>
			<br><br><img src="images/<?=$filename?>" border="0">
		<?
	}
?>
</center>
</body>
</html>
