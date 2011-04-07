#!/usr/bin/perl -T
#
# Name:
#	im-hax-crop-1.cgi.
#
# Purpose:
#	Demonstrate the Crop method.
#
# V 1.01 20-Feb-2002
# ------------------
# o Don't use $ENV{'TT_FONT_PATH'} in IM V 5.4.3.
#	Put font in same dir as script
# o Patch Apache httpd.conf to match. Ie:
#	PassEnv MAGICK_HOME
# o Remove TT_FONT_PATH from environment
#
# V 1.00 1-Oct-2001
# ------------------
# o Initial version
#
# Test environment:
#	OS: WinNT 4 SP 6a
#	Web server: Apache V 1.3.26
#	IM: V 5.4.7
#
# Input files:
#	/apache/htdocs/model.gif.
#
# Output files:
#	/apache/htdocs/modelette.gif.
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

use CGI;
use CGI::Carp qw/fatalsToBrowser/;
use Graphics::Magick;

# -----------------------------------------------

my($inner_table_color)	= '#80c0ff';
my($outer_table_color)	= '#80ffff';

# -----------------------------------------------

delete @ENV{'BASH_ENV', 'CDPATH', 'ENV', 'IFS',
	'PATH', 'SHELL'}; # For security.

my($doc_root_name)		= '/apache2/htdocs';
my($input_file_name)	= 'gm/magick.png';
my($input_path)			=
	"$doc_root_name/$input_file_name";
my($output_file_name)	= 'gm/magickette.png';
my($output_path)		=
	"$doc_root_name/$output_file_name";
my($title)				= 'Test Crop()';

my($image, $result);

my($q)		= CGI -> new();
my($html)	= [];
$image		= Graphics::Magick -> new();

push(@$html, $q -> th('Graphics::Magick') .
	$q -> td("V $Graphics::Magick::VERSION") );
push(@$html, $q -> th('Graphics::Magick -> new()') .
	$q -> td('OK') );

$result = $image -> Read($input_path) || 'OK';
push(@$html, $q -> th($input_path) .
	$q -> td($result) );
push(@$html, $q -> th('Image before') .
	$q -> td
	(
		$q -> img
		({
			name	=> 'annotate',
			src		=> "/$input_file_name",
		})
	) );

my($g)	= '70x70+50+50';
$result = $image -> Crop(geometry => $g) || 'OK';
push(@$html, $q -> th("Crop(geometry => $g)") .
	$q -> td($result) );

$result = $image -> Write($output_path) || 'OK';
push(@$html, $q -> th("Write($output_path)") .
	$q -> td($result) );
push(@$html, $q -> th('Image after') .
	$q -> td
	(
		$q -> img
		({
			name	=> 'annotate',
			src		=> "/$output_file_name",
		})
	) );
push(@$html, $q -> th('&nbsp;') .
	$q -> td('&nbsp;') );
push(@$html, $q -> th('Main Menu') .
	$q -> td
	(
		$q -> a
		({
class	=> 'submit',
href	=> '/cgi-bin/gm/im-hax-menu.cgi'
		}, 'im-hax-menu.cgi') ) );

print	$q -> header(),
		$q -> start_html({title => $title}),
		$q -> h1({align => 'center'}, $title),
		$q -> table
		(
			{
				align	=> 'center',
				bgColor	=> $inner_table_color,
			},
			$q -> Tr($html)
		),
		$q -> end_html();
