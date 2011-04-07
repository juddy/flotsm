#!/bin/bash
#
# jpegmanipulator.sh

echo "working on JPEG images only"

#set -x

#umask 022 
mkdir IMG 2>/dev/null
mkdir DAT 2>/dev/null
mkdir TAG 2>/dev/null
mkdir FULL 2>/dev/null

# Check and create pattern matching file
if [ ! -f patterns ]
then
	cat > patterns << EOF
Image
Format
Geometry
Colorspace
Resolution
Units
Filesize
Compression
Quality
Orientation
Signature
Tainted
EOF
fi

# Create HTML stub
cat > index.htm << EOF
!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>ImageCatalog  - `date`</title>
    <style type="text/css">
	<!--
	body {
	background-color: #000000;
	}
	body,td,th {
	font-family: Geneva, Arial, Helvetica, san-serif;
	font-size: 10pt;
	}
	a:link {
	color: #009900;
	}
	a:visited {
	color: #000066;
	}
	-->
	  </style>
	  </head>
	  <body "color: rgb(0, 0, 0); background-color: rgb(0, 0, 0);"
	   alink="#000099" link="#989499" vlink="#989499">
	   <br>
	   #
EOF

# ID Section
for img in *jpg 
#for img in `find ./ -iname "*jpg"| cut -d/ -f2`
do
 echo "Grabbing 128 bytes from $img"
 head -c 128 $img > DAT/$img_head.out
 echo "Identifying $img"
 identify -ping $img > DAT/"$img"_ident.out
 echo "Creating dat file for $img"
 identify -verbose -strip $img | grep --file=patterns | grep -v Version > ./DAT/${img}_ident-v.out
 #identify -verbose -$img | head -35 > DAT/"$img"_ident-v.out
 echo "Creating thumb for $img"
 convert $img -antialias -sample 400x0 +profile "*" IMG/${img}_thumb.png
 date > DAT/"$img"_date.out
 cp $img ./FULL/

	#convert -size 500x80 xc:transparent -font /usr/share/fonts/truetype/amiga4ever.ttf -pointsize 16 \
	#		-fill black -rotate 360 -annotate +23+62 `echo "$img"` \
	#		-trim +repage "$img"_title.gif
	#convert -fill white -draw 'text 5,12 "'$img'"' "$img"_title.gif
	#convert -fill white -draw 'text 5,12 "DAT/'$img'_head.out"' "$img"_head.gif
	if [ ! -f ./text2gif ]
	then
		TEXT2GIF=`which text2gif`
		if [ -z $TEXT2GIF ]
		then
			echo "No text2gif found in system.  Please install or download 'text2gif'"
			exit 1
		else
			echo "Using system 'text2gif' - $TEXT2GIF"
		fi
	else
		TEXT2GIF="./text2gif"
	fi
	echo "Generating tags and labels..."

		$TEXT2GIF -t "`cat DAT/"$img"_ident.out`" -c 180 180 180 > IMG/"$img"_ident.gif
		piece="1"
		mkdir IMG/$img-$$
		while read line
		do
			echo $line
			$TEXT2GIF -t "$line" -c 180 180 180 > ./IMG/$img-$$/"$img"_ident-v-$piece.gif
			piece=`expr $piece + 1`
		done < DAT/"$img"_ident-v.out
		#$TEXT2GIF -t "`cat DAT/"$img"_ident-v.out`" -c 180 180 180 > IMG/"$img"_ident-v.gif
		$TEXT2GIF -t "`cat DAT/"$img"_ident.out`" -c 180 180 180 > IMG/"$img"_ident.gif
		$TEXT2GIF -t "`cat DAT/"$img"_date.out`" -c 200 200 200 > IMG/"$img"_date.gif
		#$TEXT2GIF -t "`cat DAT/"$img"_head.out`" -c 128 128 128 > IMG/"$img"_head.gif
		#$TEXT2GIF -t "`cat DAT/"$img"_head.out`" -c 128 128 128 > IMG/"$img"_code.gif
		$TEXT2GIF -t "`echo "$img"`" -c 255 168 0 > IMG/"$img"_nametag.gif

	echo
	echo 'Converting' "$img" '...'
	
#HTML
#generate html
echo "Making web entry..."
   echo '<hr noshade="noshade" "height: 8px; width: 500px; margin-left: 0px; margin-right: auto;">
   <img  alt="'$img'" src="IMG/'$img'_nametag.gif"><br>
   <a href=FULL/'$img' border=0 target=blank> <img src="IMG/'$img'_thumb.png"></a>
   <br>
   <img  src="IMG/'$img'_date.gif"><br>
   <img  alt="`cat DAT/'$img'_ident-v.out`" src="./IMG/'$img'_ident-v.gif"><br>
 <hr noshade="noshade" style="height: 8px; width: 600px ; margin-left: 0px; margin-right: auto;">' >> index.htm

	for piece in `find ./IMG/$img-$$/ -name "*.gif"`
	do
	   echo '<img  src="'$piece'"><br>' >> index.htm
	done

echo '<hr noshade="noshade" style="height: 8px; width: 600px; margin-left: 0px; margin-right: auto;">
 <br>' >> index.htm
# Done with one entry
done


