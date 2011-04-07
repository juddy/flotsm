#!/usr/bin/perl -T
#
# Name:
#	im-hax-annotate-1.cgi.
#
# Purpose:
#	Demonstrate the Annotate method.
#
# V 1.03 30-Jul-2002
# ------------------
# o Loop over a number of fonts
#
# V 1.02 20-Jun-2002
# ------------------
# o Change stroke => 'red' to stroke => 'none'
#	to get sharper text.
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
#	./Generic.ttf.
#
# Output files:
#	/apache/htdocs/im-hax-annotate-1.gif.
#
# Note:
#	o tab = 4 spaces || die.
#	o Text reformatted to max 50 chars wide
#		so source can be converted to PDF
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

my($doc_root_name)	= '/apache2/htdocs';
my($title)			= 'Test Annotate()';

my($q)	 	= CGI -> new();
my($html)	= [];
my($format)	= 'png';

push(@$html, $q -> th('Graphics::Magick') .
	$q -> td("V $Graphics::Magick::VERSION") );
push(@$html, $q -> th('&nbsp;') .
	$q -> td('&nbsp;') );
push(@$html, $q -> th('Image format') .
	$q -> td($format) );
push(@$html, $q -> th('&nbsp;') .
	$q -> td('&nbsp;') );

# stroke => 'none', fill => 'red' produces
# sharper text than stroke => 'red',
# fill => 'red'.

my($font);

for $font ('Generic.ttf', 'AvantGarde-Book',
	'Bookman-Demi')
{
	my($output_file_name)	= "gm/$font.$format";
	my($output_path)		=
		"$doc_root_name/$output_file_name";
	my($image)				=
		Graphics::Magick -> new(size => '300x36');
	my($result)				=
		$image -> Read('xc:white') || 'OK';

	$result = $image -> Annotate
	(
		text		=> 'Annotation',
		x			=> 10,
		y			=> 34,
		stroke		=> 'none',
		font		=> $font,
		pointsize	=> 32,
		fill		=> 'red'
	) || 'OK';

	push(@$html, $q -> th('Font') .
		$q -> td($font) );
	push(@$html, $q -> th('Annotate(...)') .
		$q -> td($result) );

	$result =
		$image -> Write($output_path) || 'OK';

	push(@$html, $q -> th("Write(...)") .
		$q -> td($result) );
	push(@$html, $q -> th('Image') .
		$q -> td
		(
			$q -> img
			({src => "/$output_file_name"})
		) );
	push(@$html, $q -> th($q -> hr() ) .
		$q -> td($q -> hr() ) );
}

push(@$html, $q -> th('&nbsp;') .
	$q -> td('&nbsp;') );
push(@$html, $q -> th('Main Menu') .
	$q -> td
	(
		$q -> a
		({
class => 'submit',
href => '/cgi-bin/gm/im-hax-menu.cgi',
		}, 'im-hax-menu.cgi') ) );

print	$q -> header(),
		$q -> start_html($doc_root_name),
		$q -> center($q -> h1($title) ),
		$q -> table
		(
			{
				align	=> 'center',
				bgColor	=> $inner_table_color,
			},
			$q -> Tr([@$html])
		),
		$q -> end_html();
