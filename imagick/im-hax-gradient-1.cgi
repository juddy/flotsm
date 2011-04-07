#!/usr/bin/perl -T
#
# Name:
#	im-hax-gradient-1.cgi.
#
# Purpose:
#	Demonstrate the Read(gradient) method.
#
# Adapted from:
#	imagedemo1.pl by Dylan Beattie
#	http://dent.riviera.org.uk/~dylan//magick/
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
# -----------------
# o Initial version
#
# Test environment:
#	OS: WinNT 4 SP 6a
#	Web server: Apache V 1.3.26
#	IM: V 5.4.7
#
# Input files:
#	None.
#
# Output files:
#	/apache/htdocs/im-hax-gradient-1a.png.
#	/apache/htdocs/im-hax-gradient-1b.png.
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
my($output_file_name_a)	=
	'gm/im-hax-gradient-1a.png';
my($output_file_name_b)	=
	'gm/im-hax-gradient-1b.png';
my($output_path_a)		=
	"$doc_root_name/$output_file_name_a";
my($output_path_b)		=
	"$doc_root_name/$output_file_name_b";
my($title)				= 'Test Read(Gradient)';

my($image, $result);

my($q)		= CGI -> new();
my($html)	= [];
$image		= Graphics::Magick -> new;
$result		=
	$image -> Set(size => '30x180') || 'OK';

push(@$html, $q -> th('Graphics::Magick') .
	$q -> td("V $Graphics::Magick::VERSION") );
push(@$html, $q -> th('Graphics::Magick -> new()') .
	$q -> td('OK') );
push(@$html, $q -> th("Set(size => '30x180')") .
	$q -> td($result) );

my($g)	= '#ff0000-#0000ff';
$result	= $image -> Read("gradient:$g") || 'OK';
push(@$html, $q -> th("Read(gradient:$g)") .
	$q -> td($result) );

$result = $image -> Raise('3x3') || 'OK';
push(@$html, $q -> th("Raise('3x3')") .
	$q -> td($result) );

$result = $image -> Write($output_path_a)
	|| 'OK';
push(@$html, $q -> th("Write($output_path_a)") .
	$q -> td($result) );
push(@$html, $q -> th('Image') .
	$q -> td
	(
		$q -> img
		({
			name	=> 'imagedemo1',
			src		=> "/$output_file_name_a",
		})
	) );

$result = $image -> Rotate(-90) || 'OK';
push(@$html, $q -> th('Rotate(-90)') .
	$q -> td($result) );

$result = $image -> Write($output_path_b)
	|| 'OK';
push(@$html, $q -> th("Write($output_path_b)") .
	$q -> td($result) );
push(@$html, $q -> th('Image') .
	$q -> td
	(
		$q -> img
		({
			name	=> 'gradient',
			src		=> "/$output_file_name_b",
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
		$q -> start_html($doc_root_name),
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
