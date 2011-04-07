<?
/************************************************************\
*
*		PHPStego Copyright 2005 Howard Yeend
*		www.puremango.co.uk
*
*    This file is part of PHPStego.
*
*    PHPStego is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License, or
*    (at your option) any later version.
*
*    PHPStego is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with PHPStego; if not, write to the Free Software
*    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
*
\************************************************************/
// this script is (almost) totally insecure, do not rely on it for security

ini_set("max_execution_time",3000);

function is_even($num)
{
	// returns true if $num is even, false if not
	return ($num%2==0);
}

function asc2bin($char)
{
	// returns 8bit binary value from ASCII char
	// eg; asc2bin("a") returns 01100001
	return str_pad(decbin(ord($char)), 8, "0", STR_PAD_LEFT);
}

function bin2asc($bin)
{
	// returns ASCII char from 8bit binary value
	// eg; bin2asc("01100001") returns a
	// argument MUST be sent as string
	return chr(bindec($bin));
}

function rgb2bin($rgb)
{
	// returns binary from rgb value (according to evenness)
	// this way, we can store one ascii char in 2.6 pixels
	// not a great ratio, but it works (albeit slowly)

	$binstream = "";
	$red = ($rgb >> 16) & 0xFF;
	$green = ($rgb >> 8) & 0xFF;
	$blue = $rgb & 0xFF;

	if(is_even($red))
	{
		$binstream .= "1";
	} else {
		$binstream .= "0";
	}
	if(is_even($green))
	{
		$binstream .= "1";
	} else {
		$binstream .= "0";
	}
	if(is_even($blue))
	{
		$binstream .= "1";
	} else {
		$binstream .= "0";
	}

	return $binstream;
}

function steg_hide($maskfile, $hidefile)
{
	// hides $hidefile in $maskfile

	// initialise some vars
	$binstream = "";
	$recordstream = "";
	$make_odd = Array();

	// ensure a readable mask file has been sent
	$extension = strtolower(substr($maskfile['name'],-3));
	if($extension=="jpg")
	{
		$createFunc = "ImageCreateFromJPEG";
	/*
	}
	else if($extension=="png")
	{
		$createFunc = "ImageCreateFromPNG";
	} else if($extension=="gif")
	{
		$createFunc = "ImageCreateFromGIF";
	*/
	} else {
		return "Only .jpg mask files are supported";
	}

	// create images
	$pic = @ImageCreateFromJPEG($maskfile['tmp_name']);
	$attributes = @getImageSize($maskfile['tmp_name']);
	$outpic = @ImageCreateFromJPEG($maskfile['tmp_name']);

	if(!$pic || !$outpic || !$attributes)
	{
		// image creation failed
		return "cannot create images - maybe GDlib not installed?";
	}

	// read file to be hidden
	$data = file_get_contents($hidefile['tmp_name']);

	// generate unique boundary that does not occur in $data
	// 1 in 16581375 chance of a file containing all possible 3 ASCII char sequences
	// 1 in every ~1.65 billion files will not be steganographisable by this script
	// though my maths might be wrong.
	// if you really want to get silly, add another 3 random chars. (1 in 274941996890625)
	// ^^^^^^^^^^^^ would require appropriate modification to decoder.
	do
	{
		$boundary = chr(rand(0,255)).chr(rand(0,255)).chr(rand(0,255));
	} while(strpos($data,$boundary)!==false && strpos($hidefile['name'],$boundary)!==false);

	// add boundary to data
	$data = $boundary.$hidefile['name'].$boundary.$data.$boundary;
	// you could add all sorts of other info here (eg IP of encoder, date/time encoded, etc, etc)
	// decoder reads first boundary, then carries on reading until boundary encountered again
	// saves that as filename, and carries on again until final boundary reached

	// check that $data will fit in maskfile
	if(strlen($data)*8 > ($attributes[0]*$attributes[1])*3)
	{
		// remove images
		ImageDestroy($outpic);
		ImageDestroy($pic);
		return "Cannot fit ".$hidefile['name']." in ".$maskfile['name'].".<br />".$hidefile['name']." requires mask to contain at least ".(intval((strlen($data)*8)/3)+1)." pixels.<br />Maximum filesize that ".$maskfile['name']." can hide is ".intval((($attributes[0]*$attributes[1])*3)/8)." bytes";
	}

	// convert $data into array of true/false
	// pixels in mask are made odd if true, even if false
	for($i=0; $i<strlen($data) ; $i++)
	{
		// get 8bit binary representation of each char
		$char = $data{$i};
		$binary = asc2bin($char);

		// save binary to string
		$binstream .= $binary;

		// create array of true/false for each bit. confusingly, 0=true, 1=false
		for($j=0 ; $j<strlen($binary) ; $j++)
		{
			$binpart = $binary{$j};
			if($binpart=="0")
			{
				$make_odd[] = true;
			} else {
				$make_odd[] = false;
			}
		}
	}

	// now loop through each pixel and modify colour values according to $make_odd array
	$y=0;
	for($i=0,$x=0; $i<sizeof($make_odd) ; $i+=3,$x++)
	{
		// read RGB of pixel
		$rgb = ImageColorAt($pic, $x,$y);
		$cols = Array();
		$cols[] = ($rgb >> 16) & 0xFF;
		$cols[] = ($rgb >> 8) & 0xFF;
		$cols[] = $rgb & 0xFF;

		for($j=0 ; $j<sizeof($cols) ; $j++)
		{
			if($make_odd[$i+$j]===true && is_even($cols[$j]))
			{
				// is even, should be odd
				$cols[$j]++;
			} else if($make_odd[$i+$j]===false && !is_even($cols[$j])){
				// is odd, should be even
				$cols[$j]--;
			} // else colour is fine as is
		}

		// modify pixel
		$temp_col = ImageColorAllocate($outpic,$cols[0],$cols[1],$cols[2]);
		ImageSetPixel($outpic,$x,$y,$temp_col);

		// if at end of X, move down and start at x=0
		if($x==($attributes[0]-1))
		{
			$y++;
			// $x++ on next loop converts x to 0
			$x=-1;
		}
	}

	// output modified image as PNG (or other *LOSSLESS* format)
	header("Content-type: image/png");
	header("Content-Disposition: attachment; filename=encoded.png");
	ImagePNG($outpic);

	// remove images
	ImageDestroy($outpic);
	ImageDestroy($pic);
	exit();
}

function steg_recover($maskfile)
{
	// recovers file hidden in a PNG image

	$binstream = "";
	$filename = "";

	// get image and width/height
	$attributes = @getImageSize($maskfile['tmp_name']);
	$pic = @ImageCreateFromPNG($maskfile['tmp_name']);

	if(!$pic || !$attributes)
	{
		return "could not read image";
	}

	// get boundary
	$bin_boundary = "";
	for($x=0 ; $x<8 ; $x++)
	{
		$bin_boundary .= rgb2bin(ImageColorAt($pic, $x,0));
	}

	// convert boundary to ascii
	for($i=0 ; $i<strlen($bin_boundary) ; $i+=8)
	{
		$binchunk = substr($bin_boundary,$i,8);
		$boundary .= bin2asc($binchunk);
	}


	// now convert RGB of each pixel into binary, stopping when we see $boundary again

	// do not process first boundary
	$start_x = 8;

	for($y=0 ; $y<$attributes[1] ; $y++)
	{
		for($x=$start_x ; $x<$attributes[0] ; $x++)
		{
			// generate binary
			$binstream .= rgb2bin(ImageColorAt($pic, $x,$y));

			// convert to ascii
			if(strlen($binstream)>=8)
			{
				$binchar = substr($binstream,0,8);
				$ascii .= bin2asc($binchar);
				$binstream = substr($binstream,8);
			}

			// test for boundary
			if(strpos($ascii,$boundary)!==false)
			{
				// remove boundary
				$ascii = substr($ascii,0,strlen($ascii)-3);

				if(empty($filename))
				{
					$filename = $ascii;
					$ascii = "";
				} else {
					// final boundary; exit both 'for' loops
					break 2;
				}
			}
		}
		// on second line of pixels or greater; we can start at x=0 now
		$start_x = 0;
	}

	// remove image from memory
	ImageDestroy($pic);

	// and output result (retaining original filename)
	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=".$filename);
	echo $ascii;
	exit();
}

$result = true;
if(!empty($_FILES['hidefile']['tmp_name']))
{
	// encode
	$result = steg_hide($_FILES['maskfile'],$_FILES['hidefile']);
} else if(!empty($_FILES['maskfile']['tmp_name'])) {
	// decode
	$result = steg_recover($_FILES['maskfile']);
}

if($result!==true)
{
	$error = "<br /><b>Error: </b><br />".$result."<br />";
}
?>
<html>
<head>
	<title>PHP-Steg</title>
	<style>
		body{
			background: white;
			color: black;
			font-family: verdana;
			font-size: 12px;
		}
		b{
			font-size: 14px;
		}
	</style>
</head>
<body>
<b>PHP-Steg - <a href="http://www.puremango.co.uk" target="_blank">www.puremango.co.uk</a></b><br />
<br />
This is application is totally insecure(*) and is a 'concept' application rather than something to actually be employed for security.<br />
Download the <a href="php_steg_src.txt" target="_blank">source code</a> here.<br />
<?=$error?>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
<table>
<tr><td>Image</td><td><input type="file" name="maskfile"></td><td> - <b>jpg</b> for encoding, <b>png</b> for decoding</td></tr>
<tr><td>Hide file</td><td><input type="file" name="hidefile"></td><td> - leave blank if you're just decoding</td></tr>
<tr><td colspan="2" align="center"><input type="submit" value="--- go ---" onClick="this.value='processing...';"></td><td>&nbsp;</td></tr>
</table>
</form>
* - for explanation, see the PHP-steg page on puremango.co.uk (under projects)
</body>
</html>
