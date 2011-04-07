#!/usr/bin/perl
#
# Name:
#	im-hax-visible-watermark-1.pl.
#
# Purpose:
#	Demonstrate the Composite method.
#
# V 1.01 20-Feb-2002
# ------------------
# o Output to same dir as other hax
#
# V 1.00 1-Oct-2001
# -----------------
# o Initial version
#
# Test environment:
#	OS: WinNT 4 SP 6a
#	Web server: Apache V 1.3.26
#	IM: V 5.4.7
#
# Input files:
#	/apache/htdocs/model.gif
#	/apache/htdocs/smile.gif
#
# Output files:
#	/apache/htdocs/im-hax-visible-watermark-1.gif
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
#	Copyright (c) 1999-2002 Ron Savage
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

my($doc_root_name)		= '/apache2/htdocs/gm';
my($input_file_name_1)	=
	$doc_root_name . '/magick.png';
my($input_file_name_2)	=
	$doc_root_name . '/home.png';
my($output_file_name)	= $doc_root_name .
	'/im-hax-visible-watermark-1.gif';
my($image)				= Graphics::Magick -> new();
my($result)				=
	$image -> Read($input_file_name_1);
warn $result if $result;
my($mark)	= Graphics::Magick -> new();
$result		= $mark -> Read($input_file_name_2);
warn $result if $result;
my($i_h, $i_w) 			=
	$image -> Get('height', 'width');
my($mark_w, $mark_h)	=
	$mark -> Get('width', 'height');
my($x)					= $i_w - $mark_w;
my($y)					= 10;

# You can't use geometry:
#	$image -> Composite
#	(
#		image		=> $mark,
#		geometry	=> "${x}x$y",
#	);
$result					= $image -> Composite
(
	image	=> $mark,
	x		=> $x,
	y		=> $y,
);
warn $result if $result;

$result = $image -> Write($output_file_name);
warn $result if $result;
print "Success. Wrote $output_file_name. \n";