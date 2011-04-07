#!/usr/bin/perl
#
# Name:
#	im-hax-transparent-1.pl.
#
# Purpose:
#	Demonstrate the Transparent method.
#
# V 1.03 06-May-2003
# ------------------
# o Convert to GraphicsMagick V 1.0 Q8
#
# V 1.02 20-Jun-2002
# ------------------
# o Output to current directory
# o Use stroke => 'none', not '#000000'.
#	You should try both, and other colors too.
#
# V 1.01 20-Feb-2002
# ------------------
# o Output to same dir as other hax
# o Put a border round the image
#
# V 1.00 1-Oct-2001
# -----------------
# o Initial version
#
# Test environment:
#	OS: Win2K
#	Web server: Apache V 2.0.43
#	GM: V 1.0
#
# Input files:
#	Generic.ttf
#
# Output files:
#	im-hax-transparent-1.gif
#
# Note:
#	o tab = 4 spaces || die.
#	o Text reformatted to max 50 chars wide
#		so source can be converted to PDF
#
# Author:
#	Ron Savage <ron@savage.net.au>
#	http://savage.net.au/ImageMagick.html
#
# Licence:
#	Copyright (c) 1999-2003 Ron Savage
#
#	All Programs of mine are
#	'OSI Certified Open Source Software';
#	you can redistribute them and/or modify them
#	under the terms of the Artistic License,
#	a copy of which is available at:
#	http://www.opensource.org/licenses/index.html

use strict;
use warnings;

use Graphics::Magick;

# -----------------------------------------------

my($im_version) = $Graphics::Magick::VERSION;
print "Graphics::Magick V $im_version. \n";

my($work_dir_name) = '/apache2/htdocs/gm';

chdir($work_dir_name)
|| die("Can't chdir($work_dir_name): $!");

print "Work directory: $work_dir_name. \n";

my($output_file_name)	=
	'im-hax-transparent-1.png';
my($image)				=
	Graphics::Magick -> new(size => '300x70');
# Try 'xc:black' or 'gradation:black-black'.
my($result)				=
	$image -> Read('xc:green');
warn $result if $result;

# stroke => 'none', fill => 'cyan' produces
# sharper text than stroke => 'black',
# fill => 'cyan'.

$result = $image -> Annotate
(
	text		=> 'Transparent',
	x			=> 10,
	y			=> 50,
	stroke		=> 'none',
	font		=> 'Generic.ttf',
	pointsize	=> 40,
	fill		=> 'cyan',
	antialias	=> 'false',
);
warn $result if $result;

$result = $image -> Border
(
	width	=> 2,
	height	=> 2,
	fill	=> 'cyan',
);
warn $result if $result;

$result =
	$image -> Transparent(color => 'cyan');
warn $result if $result;

$result = $image -> Write($output_file_name);
warn $result if $result;
print "Success. Wrote $output_file_name. \n";