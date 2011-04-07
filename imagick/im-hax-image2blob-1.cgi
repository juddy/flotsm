#!/usr/bin/perl -T
#
# Name:
#	im-hax-image2blob-1.cgi.
#
# Purpose:
#	Demonstrate the Image2Blob.
#
# V 1.01 20-Feb-2002
# ------------------
# o Don't use $ENV{'TT_FONT_PATH'} in IM V 5.4.3.
#	Put font in same dir as script
# o Patch Apache httpd.conf to match. Ie:
#	PassEnv MAGICK_HOME
# o Remove TT_FONT_PATH from environment
#
# V 1.00 25-Jun-2001
# ------------------
# o Initial version
#
# Test environment:
#	OS: WinNT 4 SP 6a
#	Web server: Apache V 1.3.26
#	IM: V 5.4.7
#
# Input files:
#	/apache/htdocs/smile.gif.
#
# Output files:
#	none.
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

delete @ENV{'BASH_ENV', 'CDPATH', 'ENV', 'IFS',
	'PATH', 'SHELL'}; # For security.

my($doc_root_name)	= '/apache2/htdocs';
my($image_format)		= 'png';
my($image_name)			= 'gm/magick';
my($input_path)			=
"$doc_root_name/$image_name.$image_format";
my($title)				= 'Test Image2Blob()';

my($q)		= CGI -> new();
my($image)	= Graphics::Magick->new();
my($result)	= $image -> Read($input_path) || 'OK';
my(@blob)	= $image -> ImageToBlob();

print	$q -> header
		({
			type => "image/$image_format",
		}),
		$blob[0];
