#!/usr/bin/perl
#
# Name:
#	im-hax-composite-1.pl.
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
#	/apache/htdocs/smile.gif
#	/apache/htdocs/grin.gif
#	(Ie smile.gif output by im-hax-rotate-1.pl)
#
# Output files:
#	/apache/htdocs/im-hax-composite-1.gif
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
my($input_file_name_a)	=
	$doc_root_name . '/home.png';
my($input_file_name_b)	=
	$doc_root_name . '/im-hax-rotate-1.png';
my($output_file_name)	=
	$doc_root_name . '/im-hax-composite-1.png';
my($image)				= Graphics::Magick -> new();
my($result)				=
	$image -> Read($input_file_name_a);
warn $result if $result;

my($j)	= Graphics::Magick -> new();
$result	= $j -> Read($input_file_name_b);
warn $result if $result;

my($i_w, $i_h)	=
	$image -> Get('width', 'height');
my($j_w, $j_h)	= $j -> Get('width', 'height');

print "$input_file_name_a: $i_w x $i_h. \n";
print "$input_file_name_b: $j_w x $j_h. \n";

my($out_w)		= $i_w + $j_w;
my($out_h)		= $i_h; # Assume $i_h == $j_h.
# Warning: No spaces allowed around the 'x'.
my($out_size)	= "${out_w}x$out_h";
my($out)		=
	Graphics::Magick -> new(size => $out_size);
$result			= $out -> Read('xc:white');
warn $result if $result;

print "$output_file_name: $out_w x $out_h. \n";

$result	= $out -> Composite
(
	compose	=> 'Over',
	image	=> $image,
	x		=> 0,
	y		=> 0,
);
warn $result if $result;

$result	= $out -> Composite
(
	compose	=> 'Over',
	image	=> $j,
	x		=> $i_w,
	y		=> 0,
);
warn $result if $result;

$result = $out -> Write($output_file_name);
warn $result if $result;
print "Success. Wrote $output_file_name. \n";