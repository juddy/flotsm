<?

/*
	flotsm_class.php; based on:
	---------------------------	
	ImageMagick class 1.0
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

	class ImageMagick {

		var $targetdir      = '';
		var $imagemagickdir = '/usr/bin';
		var $temp_dir		= '../'; // httpd must be able to write there
		var $file_history   = array();
		var $temp_file      = '';
		var $jpg_quality	= '100';
		var $count			= 0;
		var $image_data     = array();
		var $error          = '';
		var $verbose        = FALSE;



			/*
			 	Constructor places uploaded file in $this->temp_dir
			 	Gets the imagedata and stores it in $this->image_data
			 	$filedata = $_FILES['file1']
			*/

			function ImageMagick($filedata) {
				$this->temp_file = time().ereg_replace("[^a-zA-Z0-9_.]", '_', $filedata['name']);
				if(!@rename($filedata['tmp_name'], $this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file))
					die("Imagemagick: Upload failed");
				$this->file_history[] = $this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file;
				$this->GetSize();
			}




			/*

				setTargetdir(string string)
				Sets the dir to where the images are saved
				httpd user must have write access there
			*/

			function setTargetdir($target) {
				if($target == '')
					$this->targetdir = $this->temp_dir;
				else
					$this->targetdir = $target;
				if($this->verbose == TRUE) {
					echo "Set target dir to '{$this->targetdir}'\n";
				}
			}




			/*
				string getFilename()
				Returns the current filename
			*/

			function getFilename() {
				return $this->temp_file;
			}




			/*
				setVerbose(bool)
				if set to TRUE, all information is displayed
			*/

			function setVerbose($v=FALSE) {
				$this->verbose = $v;
				if($v == TRUE) {
					echo '<pre>';
				}
			}




			/*
				array GetSize()
				returns the size of the image in an array
				array[0] = x-size
				array[1] = y-size
			*/

			function GetSize() {
				$command = $this->imagemagickdir."/identify -verbose '".$this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file."'";
				exec($command, $returnarray, $returnvalue);
				if($returnvalue)
					die("ImageMagick: Corrupt image");
				else {
					$num = count($returnarray);
					for($i=0;$i<$num;$i++)
						$returnarray[$i] = trim($returnarray[$i]);
					$this->image_data = $returnarray;
				}
				$num = count($this->image_data);
				for($i=0;$i<$num;$i++)
					if(eregi('Geometry', $this->image_data[$i])) {
						$tmp1 = explode(' ', $this->image_data[$i]);
						$tmp2 = explode('x', $tmp1[1]);
						$this->size = $tmp2;
						return $tmp2;
					}
			}




			/*
				Flip(string string)
				flips the image
				possible arguments:
					'horizontal' > flips the image horizontaly
					'vertical' > flips the image verticaly
				default is horizontal
			*/

			function Flip($var='horizontal') {

				if($this->verbose == TRUE) {
					echo "Flip:\n";
				}
				$tmp = $var=='horizontal'?'-flop':($var=='vertical'?'-flip':'');
				if($this->verbose == TRUE) {
					echo "  Method: {$var}\n";
				}
				$command = "{$this->imagemagickdir}/convert {$tmp} '{$this->temp_dir}/tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}/tmp".++$this->count."_{$this->temp_file}'";
				if($this->verbose == TRUE) {
					echo "  Command: {$command}\n";
				}
				exec($command, $returnarray, $returnvalue);
				if($returnvalue) {
					$this->error .= "ImageMagick: Flip failed\n";
					if($this->verbose == TRUE) {
						echo "Flip failed\n";
					}
				} else {
					$this->file_history[] = $this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file;
				}
			}




			/*
				Dithers the image
			*/

			function Dither() {

				if($this->verbose == TRUE) {
					echo "Dither:\n";
				}
				$command = "{$this->imagemagickdir}/convert -dither '{$this->temp_dir}/tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}/tmp".++$this->count."_{$this->temp_file}'";
				if($this->verbose == TRUE) {
					echo "  Command: {$command}\n";
				}
				exec($command, $returnarray, $returnvalue);
				if($returnvalue) {
					$this->error .= "ImageMagick: Dither failed\n";
					if($this->verbose == TRUE) {
						echo "Dither failed\n";
					}
				} else {
					$this->file_history[] = $this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file;
				}
			}




			/*
				Converts the image to monochrome (2 color black-white)
			*/

			function Monochrome() {

				if($this->verbose == TRUE) {
					echo "Monochrome:\n";
				}
				$command = "{$this->imagemagickdir}/convert -monochrome '{$this->temp_dir}/tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}/tmp".++$this->count."_{$this->temp_file}'";
				if($this->verbose == TRUE) {
					echo "  Command: {$command}\n";
				}
				exec($command, $returnarray, $returnvalue);
				if($returnvalue) {
					$this->error .= "$ImageMagick: Monochrome failed\n";
					if($this->verbose == TRUE) {
						echo "Monochrome failed\n";
					}
				} else {
					$this->file_history[] = $this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file;
				}
			}




			/*
				Converts the image to it's negative
			*/

			function Negative() {

				if($this->verbose == TRUE) {
					echo "Negative:\n";
				}
				$command = "{$this->imagemagickdir}/convert -negate '{$this->temp_dir}/tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}/tmp".++$this->count."_{$this->temp_file}'";
				if($this->verbose == TRUE) {
					echo "  Command: {$command}\n";
				}
				exec($command, $returnarray, $returnvalue);
				if($returnvalue) {
					$this->error .= "ImageMagick: Negative failed\n";
					if($this->verbose == TRUE) {
						echo "Negative failed\n";
					}
				} else {
					$this->file_history[] = $this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file;
				}
			}




			/*
				Rotate(float value, string string, string string)
				Rotates the image
				possible values for arg1:
					numbers from 0-360
				possible values for arg2:
					hexadecimal color without the "#" for example: C3D6A0
				possible values for arg3:
					no value > standard rotation
					'morewidth' > rotates the image only if only if its width exceeds the height
					'lesswidth' > rotates the image only if its width is less than the height
			*/

			function Rotate($deg=90, $bgcolor='000000', $how='') {

				$tmp = $how=='lesswidth'?"<":($how=='morewidth'?">":'');
				if($this->verbose == TRUE) {
					echo "Rotate:\n";
					echo "  Degrees: {$deg}\n";
					echo "  Method: ".($how==''?'standard':$how)."\n";
					echo "  Background color: #{$bgcolor}\n";
				}
				$command = "{$this->imagemagickdir}/convert -background '#{$bgcolor}' -rotate '{$deg}{$tmp}' '{$this->temp_dir}/tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}/tmp".++$this->count."_{$this->temp_file}'";
				if($this->verbose == TRUE) {
					echo "  Command: {$command}\n";
				}
				exec($command, $returnarray, $returnvalue);
				if($returnvalue) {
					$this->error .= "ImageMagick: Rotate failed\n";
					if($this->verbose == TRUE) {
						echo "Rotate failed\n";
					}
				} else {
					$this->file_history[] = $this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file;
				}
			}




			/*
				Blur(int value, int value)
				blur the image with a gaussian operator
				arg1 > radius
				arg2 > sigma
			*/

			function Blur($radius=5, $sigma=2) {

				if($this->verbose == TRUE) {
					echo "Blur:\n";
					echo "  Radius: {$radius}\n";
					echo "  Sigma: {$sigma}\n";
				}
				$command = "{$this->imagemagickdir}/convert -blur '{$radius}x{$sigma}' '{$this->temp_dir}/tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}/tmp".++$this->count."_{$this->temp_file}'";
				if($this->verbose == TRUE) {
					echo "  Command: {$command}\n";
				}
				exec($command, $returnarray, $returnvalue);
				if($returnvalue) {
					$this->error .= "ImageMagick: Blur failed\n";
					echo "Blur failed\n";
				} else {
					$this->file_history[] = $this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file;
				}
			}




			/*
				Frame(int value, sting string)
				Draws a frame around the image
				arg1 > frame width in pixels
				arg2 > frame color in hexadecimal, for exaple: 4AF2C9
			*/

			function Frame($width=12, $color='666666') {

				if($this->verbose == TRUE) {
					echo "Frame:\n";
					echo "  Width: {$width}\n";
					echo "  Color: {$color}\n";
				}
				$command = "{$this->imagemagickdir}/convert -mattecolor '#{$color}' -frame '{$width}x{$width}' '{$this->temp_dir}/tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}/tmp".++$this->count."_{$this->temp_file}'";
				if($this->verbose == TRUE) {
					echo "  Command: {$command}\n";
				}
				exec($command, $returnarray, $returnvalue);
				if($returnvalue) {
					$this->error .= "ImageMagick: Frame failed\n";
					if($this->verbose == TRUE) {
						echo "Frame failed\n";
					}
				} else {
					$this->file_history[] = $this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file;
				}
			}




			/*
				Resize(int value, int value, string string)
				Resize the image to given size
				possible values:
					arg1 > x-size, unsigned int
					arg2 > y-size, unsigned int
					arg3 > resize method;
								'keep_aspect' > changes only width or height of image
								'fit' > fit image to given size
			*/

			function Resize($x_size, $y_size, $how='keep_aspect') {

				if($this->verbose == TRUE) {
					echo "Resize:\n";
				}

				$method = $how=='keep_aspect'?'>':($how=='fit'?'!':'');

				if($this->verbose == TRUE) {
					echo "  Resize method: {$how}\n";
				}

				$command = "{$this->imagemagickdir}/convert -geometry '{$x_size}x{$y_size}{$method}' '{$this->temp_dir}/tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}/tmp".++$this->count."_{$this->temp_file}'";

				if($this->verbose == TRUE) {
					echo "  Command: {$command}\n";
				}

				exec($command, $returnarray, $returnvalue);

				if($returnvalue) {
					$this->error .= "ImageMagick: Resize failed\n";
					if($this->verbose == TRUE) {
						echo "Resize failed\n";
					}
				} else {
					$this->file_history[] = $this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file;
				}
			}




			/*
				Square(string string)
				Makes the image a square
				possible arguments:
					'center' > crops to a square in the center of the image
					'left' > crops to a square on the left side of the image
					'right' > crops to a square on the right side of the image
			*/

			function Square($how='center') {

				$this->size = $this->GetSize();
				if($how == 'center') {
					if($this->size[0] > $this->size[1])
						$line = $this->size[1].'x'.$this->size[1].'+'.round((($this->size[0] - $this->size[1]) / 2)).'+0';
					else
						$line = $this->size[0].'x'.$this->size[0].'+0+'.round((($this->size[1] - $this->size[0])) / 2);
				}
				if($how == 'left') {
					if($this->size[0] > $this->size[1])
						$line = $this->size[1].'x'.$this->size[1].'+0+0';
					else
						$line = $this->size[0].'x'.$this->size[0].'+0+0';
				}
				if($how == 'right') {
					if($this->size[0] > $this->size[1])
						$line = $this->size[1].'x'.$this->size[1].'+'.($this->size[0]-$this->size[1]).'+0';
					else
						$line = $this->size[0].'x'.$this->size[0].'+0+'.($this->size[1] - $this->size[0]);
				}

				$command = "{$this->imagemagickdir}/convert -crop '{$line}' '{$this->temp_dir}/tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}/tmp".++$this->count."_{$this->temp_file}'";

				if($this->verbose == TRUE) {
					echo "Square:\n";
					echo "  Method: {$how}\n";
					echo "  Command: {$command}\n";
				}
				exec($command, $returnarray, $returnvalue);
				if($returnvalue) {
					$this->error .= "ImageMagick: Square failed\n";
					if($this->verbose == TRUE) {
						echo "Square failed\n";
					}
				} else {
					$this->file_history[] = $this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file;
				}
			}




			/*
				Crop(int value, int value, string string)
				Crops the image to given size
				arg1 > x-size, unsigned int
				arg2 > y-size, unsigned int
				arg3 > method;
						'center', crops the image leaving the center
						'left', crops only from the right side
						'right', crops only from the left side
			*/

			function Crop($size_x, $size_y, $how='center') {

				if($this->verbose == TRUE) {
					echo "Crop:\n";
				}

				$this->size = $this->GetSize();

				if($size_x > $this->size[0]) {
					$size_x = $this->size[0];
				}

				if($size_y > $this->size[1]) {
					$size_y = $this->size[1];
				}

				if($this->verbose == TRUE) {
					echo "  Args: size_x = {$size_x}\n";
					echo "  Args: size_y = {$size_y}\n";
					echo "  Crop method: {$how}\n";
					echo "  GetSize: size_x = {$this->size[0]}\n";
					echo "  GetSize: size_y = {$this->size[1]}\n";
				}

				if($how == 'center') {
					$line = $size_x.'x'.$size_y.'+'.round( ($this->size[0] - $size_x) / 2 ).'+'.round((($this->size[1] - $size_y) / 2));
				}

				if($how == 'left') {
					$line = $size_x.'x'.$size_y.'+0+0';
				}

				if($how == 'right') {
					$line = $size_x.'x'.$size_y.'+'.($this->size[0] - $size_x).'+0';
				}

				$command = "{$this->imagemagickdir}/convert -crop '{$line}' '{$this->temp_dir}/tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}/tmp".++$this->count."_{$this->temp_file}'";

				if($this->verbose==1) {
					echo "  Command: {$command}\n";
				}

				exec($command, $returnarray, $returnvalue);

				if($returnvalue) {
					$this->error .= "ImageMagick: Crop failed\n";
					if($this->verbose == TRUE) {
						echo "Crop failed\n";
					}
				} else {
					$this->file_history[] = $this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file;
				}
		   }


			/*
				Convert(string string)
				Converts the image to any format using the given file extension
				Defaults to jpg
			*/

			function Convert($format='jpg') {

				if($this->verbose == TRUE) {
					echo "Convert:\n";
				}

				$name = explode('.' , $this->temp_file);
				$name = "{$name[0]}.{$format}";

				if($this->verbose == TRUE) {
					echo "  Desired format: {$format}\n";
					echo "  Constructed filename: {$name}\n";
				}

				$command = "{$this->imagemagickdir}/convert -colorspace RGB -fill '#fff' -draw 'roundRectangle  5,5 101,45 5,5' -draw 'image Over 10,10 86,29 images/flotsm.png' -quality {$this->jpg_quality} '{$this->temp_dir}/tmp{$this->count}_{$this->temp_file}' '{$this->temp_dir}/tmp".++$this->count."_{$name}'";

				if($this->verbose == TRUE) {
					echo "  Command: {$command}\n";
				}

				exec($command, $returnarray, $returnvalue);

				$this->temp_file = $name;
				
				if($returnvalue) {
					$this->error .= "ImageMagick: Convert failed\n";
					if($this->verbose == TRUE) {
						echo "Convert failed\n";
					}
				} else {
					$this->file_history[] = $this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file;
				}
			}




			/*
				string string Save(string string)
				Saves the image to the targetdir, returning the filename
				arg1 > set prefix, for example : 'thumb_'
			*/

			function Save($prefix='') {

				if($this->verbose == TRUE) {
					echo "Save:\n";
				}

				if(!@copy($this->temp_dir.'/tmp'.$this->count.'_'.$this->temp_file, $this->targetdir.'/'.$prefix.$this->temp_file)) {
					$this->error .= "ImageMagick: Couldn't save to {$this->targetdir}/'{$prefix}{$this->temp_file}\n";
					if($this->verbose == TRUE) {
						echo "Save failed to {$this->targetdir}/{$prefix}{$this->temp_file}\n";
					}
				} else {
					if($this->verbose == TRUE) {
						echo "  Saved to {$this->targetdir}/{$prefix}{$this->temp_file}\n";
						echo "  Created card entry for {$this->targetdir}/{$prefix}{$this->temp_file}\n";
					}
				}
				return $prefix.$this->temp_file;
			}




			/*
				Cleans up all the temp data in $this->tempdir
			*/

			function Cleanup() {

				if($this->verbose == TRUE) {
					echo "Cleanup:\n";
				}

				$num = count($this->file_history);

				for($i=0;$i<$num;$i++) {
					if(!unlink($this->file_history[$i])) {
						$this->error .= "ImageMagick: Removal of temporary file '{$this->file_history[$i]}' failed\n";
						if($this->verbose == TRUE) {
							echo "  Removal of temporary file '{$this->file_history[$i]}' failed\n";
						}
					} else {
						if($this->verbose == TRUE) {
							echo "  Deleted temp file: {$this->file_history[$i]}\n";
						}
					}
				}

				if($this->verbose == TRUE) {
					echo '</pre>';
				}
			}

	}
?>