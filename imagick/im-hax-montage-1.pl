#!/usr/bin/perl
#
# Name:
#	im-hax-montage-1.pl.
#
# Purpose:
#	Demonstrate the Montage method.
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
#	./Generic.ttf
#	/apache/htdocs/model.gif
#	/apache/htdocs/smile.gif
#
# Output files:
#	/apache/htdocs/im-hax-montage-1.gif
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
	$doc_root_name . '/home.png';
my($input_file_name_2)	=
	$doc_root_name . '/magick.png';
my($output_file_name)	=
	$doc_root_name . '/im-hax-montage-1.png';
my($stack)				= Graphics::Magick -> new();
my($image)				= Graphics::Magick -> new();
my($result)				=
	$image -> Read($input_file_name_1);
warn $result if $result;

my($j)	= Graphics::Magick -> new();
$result	= $j -> Read($input_file_name_2);
warn $result if $result;

push(@$stack, $image);
push(@$stack, $j);

my($final) = $stack -> Montage
(
	title		=> 'model + smile',
	label		=> 'model,smile',
	font		=> 'Generic.ttf',
	pointsize	=> 32,
	tile		=> '2x2',
	stroke		=> 'red',
	shadow		=> 'true',
	borderwidth	=> 2,
	frame		=> '2x2',
);

$result = $final -> Write($output_file_name);
warn $result if $result;
print "Success. Wrote $output_file_name. \n";
