#!/usr/bin/perl
#
# Name:
#	im-hax-composite-2.pl.
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
#	/apache/htdocs/old-pix/*
#
# Output files:
#	/apache/htdocs/new-pix/*
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

my($doc_root_name)	= '/apache2/htdocs/gm';
my($old_dir_name)	=
	$doc_root_name . '/old-pix';
my($new_dir_name)	=
	$doc_root_name . '/new-pix';

opendir(INX, $old_dir_name);
my(@pix) = grep{! /^\./} readdir(INX);
closedir(INX);

die("$old_dir_name does not contain any images")
	if ($#pix < 0);

my($composite_op)			= 'Over';
my($water_mark_file_name)	=
	$doc_root_name . '/home.png';
my($water_mark)				=
	Graphics::Magick -> new();
my($result)					=
	$water_mark -> Read($water_mark_file_name);
print	"Error: Image: ",
		"$water_mark_file_name. $result. \n"
		if ($result);
my($m_width, $m_height, $m_size, $m_fmt) =
	$water_mark -> Ping($water_mark_file_name);

for (@pix)
{
	my($image)							=
		Graphics::Magick -> new();
	my($width, $height, $size, $fmt)	=
		$image -> Ping("$old_dir_name/$_");

	print	"Reading $_ ",
			"(geometry => $width x $height, ",
			"size => $size, format => $fmt)",
			"... \n";
	$result = $image -> Read("$old_dir_name/$_");
	print	"Error: Image: $_. $result. \n"
			if ($result);

	my($x)	= int( ($width - $m_width) / 2);
	my($y)	= int( ($height - $m_height) / 2);
	$result	= $image -> Composite
	(
		compose	=> $composite_op,
		image	=> $water_mark,
		x		=> $x,
		y		=> $y,
	);
	print	"Error: Image: $_. $result. \n"
			if ($result);
	print	"Composed. Image: $_. ",
			"Operation: $composite_op. \n";

	$result = $image->Write("$new_dir_name/$_");
	print	"Error: Image: $_. $result. \n"
			if ($result);
	print "Success: Wrote $new_dir_name/$_. \n";
	print "\n";
}
