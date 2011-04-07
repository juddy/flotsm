#!/usr/bin/perl -T
#
# Name:
#	im-hax-menu.cgi.
#
# Purpose:
#	Provide a simple menu system for the hax.
#
# V 1.01 20-Feb-2002
# ------------------
# o Restrict menu to *.cgi files
#
# V 1.00 17-Jan-2002
# ------------------
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
#	None.
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

use CGI qw/nobr/;
use CGI::Carp qw/fatalsToBrowser/;

# -----------------------------------------------

my($inner_table_color)	= '#80c0ff';
my($outer_table_color)	= '#80ffff';

# -----------------------------------------------

delete @ENV{'BASH_ENV', 'CDPATH', 'ENV', 'IFS',
	'PATH', 'SHELL'}; # For security.

my($q)			= CGI -> new();
my($html)		= [];
my($title)		= 'ImageMagick Hax Menu';
my($dir_name)	= '/apache2/cgi-bin/gm';

opendir(INX, $dir_name) ||
	die("Can't opendir($dir_name): $!");
my(@menu) = sort grep
				{
					/^im-hax.+\.cgi$/ &&
					! /im-hax-menu/
				} readdir(INX);
closedir(INX);

# Put an even # of items into @menu.

if ( ($#menu % 2) == 0)
{
	my($script)	= $q -> url();
	$script		=~ s/.+\///;

	push(@menu, $script);
}

print	$q -> header(),
		$q -> start_html({title => $title}),
		$q -> table
		(
			{
				align	=> 'center',
				bgColor	=> $inner_table_color,
			},
			$q -> caption($title),
			$q -> Tr
			([
				map
				{
					my($i) = 2 * $_;

					$q -> td
					(
						$q -> a
						({
							href	=> $menu[$i],
						}, $menu[$i])
					) .
					$q -> td
					(
						$q -> a
						({
							href => $menu[$i+1],
						}, $menu[$i + 1])
					),
					$q -> td('&nbsp;')
				} 0 .. int($#menu / 2)
			])
		),
		$q -> end_html();
