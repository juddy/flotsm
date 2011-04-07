# ident Section
for img in *[jpg,JPG]
	do
		
		head -c 128 $img > "$img"_head.out
	       
   		identify -ping $img > "$img"_ident.out
		
		convert $img -antialias -sample 360x360 +profile "*" "$img"_thumb.png
					
                date > "$img"_date.out
	
		./text2gif -t "`cat "$img"_ident.out`" -c 180 180 180 > "$img"_ident.gif
		
		./text2gif -t "`cat "$img"_date.out`" -c 200 200 200 > "$img"_date.gif

		./text2gif -t "`cat "$img"_head.out`" -c 128 128 128 > "$img"_head.gif
		
		./text2gif -t "`cat "$img"_head.out`" -c 128 128 128 > "$img"_code.gif
		
		./text2gif -t "`echo "$img"`" -c 255 168 0 > "$img"_nametag.gif
		               echo "Generating Nametag..."
	echo
	echo 'Converting' "$img" '...'
	
	
#HTML
#generate buttons
   echo '<hr noshade="noshade" style="height: 8px; width: 500px; margin-left: 0px; margin-right: auto;">
   <img style= alt="'$img'" src="'$img'_nametag.gif"><br>
   <a href='$img' target=_blank> <img src="'$img'_thumb.png"></a>
   <img style= alt="`date`" src="'$img'_date.gif"><br>
   <img style= alt="`cat '$img'_head.out`" src="'$img'_head.gif"><br>
 <hr noshade="noshade" style="height: 8px; width: 360px; margin-left: 0px; margin-right: auto;">
 <br>' >> index.htm

	echo "Making web entry..."
done
