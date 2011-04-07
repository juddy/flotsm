# Name:
#	readme.txt
#
# Author:
#	Ron Savage <ron@savage.net.au>
#
# Home:
#	http://savage.net.au/ImageMagick.html

Overview
--------
o	ImageMagick Hax are a set of Perl scripts aimed at people beginning to use
	the Perl module Image::Magick. The aim is to get you up and running as
	quickly as possible.

o	You are expected to study the scripts before running them. Hence the
	promise: No surprises.

o	Each hax is deliberately designed to be trivial, so as to help focus on
	one feature of ImageMagick. The name of the hax tells you the point of
	the exercise. Eg: im-hax-crop-1.pl demonstrates Crop(...).
	It's that simple. This helps deliver on the promise: No surprises.

o	*.cgi are CGI scripts, and *.pl are command line scripts. A simple menu
	script, im-hax-menu.cgi, is supplied to help run the CGI scripts.

o	Comments at the start of each hax list the assumed directory structure,
	input files and output files. These are also stored in variables at the
	start of each script, to facilitate installation anywhere.

o	All input files are either shipped with ImageMagick or output from a hax.
	Eg: The output of im-hax-rotate-1.pl is input to im-hax-composite-1.pl.

o	Only the generic TTF file, Generic.ttf is used to annotate images. Nothing
	would be gained here by using a different font file.

Files
-----
o	im-hax-1-13.tgz contains ImageMagick Hax 1 .. 13, as well as the CGI menu,
	licence.txt and this file, readme.txt.

o	im-hax-14-26.tgz contains ImageMagick Hax 14 .. 26, as well as the CGI menu,
	licence.txt and this file, readme.txt.

o	im-hax-27-39.tgz contains ImageMagick Hax 27 .. 39, as well as the CGI menu,
	licence.txt and this file, readme.txt.
