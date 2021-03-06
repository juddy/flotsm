//
// $Id: webmagick.js,v 1.39 2001/11/24 21:29:03 ache Exp $
//
// JavaScript routines to display a set of images
// Part of the WebMagick package
// Copyright 1997, 1998, Bob Friesenhahn
//

// Start with first page (zero based)
var pageNumber = 0;
// Start with first image on page
var pageImageIndex = 0;


//
// Window size hints

var browserWindowWidth = 800;
var browserWindowHeight = 500;
// Netscape 4.X
if( parent.innerWidth &&  parent.innerHeight) {
  browserWindowWidth = parent.innerWidth;
  browserWindowHeight = parent.innerHeight;
}
//alert('Width: ' + browserWindowWidth + '  Height: ' + browserWindowHeight);

///////////////////////////////////////////////////
//
// Routines and variables for the master frame definition
//
///////////////////////////////////////////////////

// Define window names based on frame style option
var frameTargets = new Object();

var origFramestyle = htmlOpts["framestyle"];
var origColumns = htmlOpts["columns"];
var origRows = htmlOpts["rows"];
var today = new Date();
var expires_date = new Date(today.getYear() + 1,today.getMonth(),today.getDate(),0,0,0);

// for testing
//Set_Cookie('framestyle', '2', expires_date);
//Set_Cookie('columns', '2', expires_date);
//Set_Cookie('rows', '3', expires_date);

// Should check to see if all of these are needed
if (htmlOpts["config"] && htmlOpts["config"] != 0 && htmlOpts["tables"]) {
	if (Get_Cookie('framestyle') != null) {
		htmlOpts["framestyle"] = Get_Cookie('framestyle');
		if (htmlOpts["framestyle"] != origFramestyle) {
			// reset the cookie to increase the expiration	
			// should do something about the path, but I'm not sure what - one of our current options?
			Set_Cookie('frameStyle', htmlOpts["framestyle"], expires_date);	
		}
	}
	if (Get_Cookie('rows') != null) {
		htmlOpts["rows"] = Get_Cookie('rows');
		if (htmlOpts["rows"] != origRows) {		
			Set_Cookie('rows', htmlOpts["rows"], expires_date);	
		}
	}
	if (Get_Cookie('columns') != null) {	
		htmlOpts["columns"] = Get_Cookie('columns');
		if (htmlOpts["columns"] != origColumns) {
			Set_Cookie('columns', htmlOpts["columns"], expires_date);	
		}
	}
	
	if ((htmlOpts["rows"] != origRows) || (htmlOpts["columns"] != origColumns)) {		
		var totalCount = 0;
		for (i = 0; i < imageNames.length; i++) {
			for (j = 0; j < imageNames[i].length; j++) {
				totalCount++;			
			}		
		}		
		var perPage = htmlOpts["rows"] * htmlOpts["columns"];		
		var remainder = totalCount % perPage;
		htmlOpts["numpages"] = Math.floor(totalCount / perPage);
		if (remainder != 0) { 
			htmlOpts["numpages"]++;
		}

		imLabels = fixDoubleArray(imLabels, remainder);
		thumbwidths = fixDoubleArray(thumbwidths, remainder);
		thumbheights = fixDoubleArray(thumbheights, remainder);
		imageNames = fixDoubleArray(imageNames, remainder);
		
		// why does this run twice????
		//alert ('imageNames ' + imageNames[0]);
	}
}
		
// config only works with tables
if  (htmlOpts["config"] && !htmlOpts["tables"]) {
	htmlOpts["config"] = 0;
	}

// Set number of display windows based on framestyle option
var numWindows = 3; // default for framestyle 1
if( htmlOpts["framestyle"] == 2 ||
    htmlOpts["framestyle"] == 3 || 
    htmlOpts["framestyle"] == 4) {
  numWindows = 4;
}

// Set window names based on number of display windows
if( numWindows == 3 ) {
  // One window plays triple duty when there are only two frames
  frameTargets["dirview"]    = 'dirview';
  frameTargets["imageview"]  = 'imageview';
  frameTargets["navview"]    = 'navview';
  frameTargets["readmeview"] = 'imageview';
  frameTargets["thumbview"]  = 'imageview';
}
else if( numWindows == 4 ) {
  // When there are four frames, only the README shares a window
  frameTargets["dirview"]    = 'dirview';
  frameTargets["imageview"]  = 'imageview';
  frameTargets["navview"]    = 'navview';
  frameTargets["readmeview"] = 'imageview';
  frameTargets["thumbview"]  = 'thumbview';
}

function emptyHTML()
{
	return '<HTML><HEAD></HEAD><BODY></BODY></HTML>';
		
}


function drawWindows()
{
	var style = htmlOpts["framestyle"];

  // Is README actually displayed?
  if( ( htmlOpts["readmepresent"] == 1 && htmlOpts["numpages"] == 0 )
      || htmlOpts["readmevisible"] == 1 ) {
    var showReadme = 1;
  } else {
    var showReadme = 0;
  }

	if( style == 1 ) {  	
		displayResult(frameTargets["dirview"], returnDirectoryHTML());
		displayResult(frameTargets["navview"], returnNavHTML(0));
		if (showReadme != 1) {
			displayResult(frameTargets["imageview"], returnThumbNailsHTML());
		}
	} else if (style == 2) {
		displayResult(frameTargets["dirview"], returnDirectoryHTML());
		displayResult(frameTargets["thumbview"], returnThumbNailsHTML());
		displayResult(frameTargets["navview"], returnNavHTML(0));
		if (showReadme != 1) {
			displayResult(frameTargets["imageview"], returnImageHTML(0));
		}
	} else if (style == 3) {
		displayResult(frameTargets["dirview"], returnDirectoryHTML());
		displayResult(frameTargets["thumbview"], returnThumbNailsHTML());
		displayResult(frameTargets["navview"], returnNavHTML(0));
		if (showReadme != 1) {
			displayResult(frameTargets["imageview"], returnImageHTML(0));
		}
	} else if (style == 4) {
		displayResult(frameTargets["thumbview"], returnThumbNailsHTML());
		displayResult(frameTargets["navview"], returnNavHTML(0));
		displayResult(frameTargets["dirview"], returnDirectoryHTML());
		if (showReadme != 1) {
			displayResult(frameTargets["imageview"], returnImageHTML(0));
		}
	}
	
}

function displayResult(target, result)
{	
	var theFrame = open("", target);
	theFrame.document.writeln(result);
	theFrame.document.close();	
}

function returnFrameHTML()
{
  var style = htmlOpts["framestyle"];

  // Is README actually displayed?
  if( ( htmlOpts["readmepresent"] == 1 && htmlOpts["numpages"] == 0 )
      || htmlOpts["readmevisible"] == 1 ) {
    var showReadme = 1;
  } else {
    var showReadme = 0;
  }

  // frame border definition
  var frameBorderSize =
    ' FRAMEBORDER=' + htmlOpts["frameborder"] + ' BORDER=' + htmlOpts["framebordersize"];
  // margin width definition
  var marginBorderSize =
    ' MARGINWIDTH=' + htmlOpts["framemarginwidth"] + ' MARGINHEIGHT=' + htmlOpts["framemarginheight"];

  // Javascript URLs to generate frame source
  var srcDirectory	= ' SRC="javascript:parent.emptyHTML();"';
  var srcHidden         = ' SRC="' + htmlOpts["jspageindex"] + '"';
  var srcImageView	= ' SRC="javascript:parent.emptyHTML();"';
  var srcNavView        = ' SRC="javascript:parent.emptyHTML();"';
  var srcReadMe         = ' SRC="' + htmlOpts["readme"] + '"';
  var srcThumbnails	= ' SRC="javascript:parent.emptyHTML();"';

  // frame names
  var nameDirView 	= ' NAME="' + frameTargets["dirview"] + '"';
  var nameHidden	= ' NAME="webmagick"';
  var nameImageView 	= ' NAME="' + frameTargets["imageview"] + '"';
  var nameNavView 	= ' NAME="' + frameTargets["navview"] + '"';
  var nameThumbView 	= ' NAME="' + frameTargets["thumbview"] + '"';

  var result = 
    '<HTML>\n' +
    '<HEAD>\n' +
    '<TITLE>' + htmlOpts["title"] + '</TITLE>\n';
    if (htmlOpts["stylesheet"])
     result += '<LINK REL="stylesheet" type="text/css" HREF="' + htmlOpts["stylesheet"] + '">\n';
    result += '</HEAD>\n';

  if( style == 1 ) {
    // Three frame screen with directories listed in the
    // top-left frame, navigation icons displayed in top right frame,
    // and imagemap or README (dual-use) displayed in the bottom frame.
    //
    //  -------------
    // |  |          |
    // |-------------|
    // |             |
    // |             |
    // |             |
    // |             |
    //  -------------
    result +=
      '<FRAMESET COLS="132,*"' + frameBorderSize + '>\n' +
      ' <FRAME' + srcDirectory + nameDirView + marginBorderSize + '>\n' +
      ' <FRAMESET ROWS="50,*,0"' + frameBorderSize + '>\n' +
      '  <FRAME' + srcNavView + nameNavView + ' MARGINHEIGHT="0" ' + 'SCROLLING="no" NORESIZE ' + '>\n';
    if( showReadme == 1 ) {
      result += '  <FRAME' + srcReadMe + nameImageView + marginBorderSize + '>\n';
    } else {
      result += '  <FRAME' + srcThumbnails + nameImageView + marginBorderSize + '>\n';
    }
    result +=
      '  <FRAME' + srcHidden + nameHidden + ' SCROLLING="no" NORESIZE >\n' +
      ' </FRAMESET>\n' +
      '</FRAMESET>\n';

  } else if ( style == 2 ) {
    // Four frame screen with directories listed in top-left frame,
    // image navigation icons in top right frame, imagemap displayed
    // in bottom-left frame, and README/Images displayed in
    // full-height right-hand frame.
    //  -------------
    // |  |----------|
    // |  |          |
    // |--|          |
    // |  |          |
    // |  |          |
    // |  |          |
    //  -------------
    result +=
      '<FRAMESET COLS="145,*,0"' + frameBorderSize + '>\n' +
      ' <FRAMESET ROWS="20%,*"' + frameBorderSize + '>\n' +
      '  <FRAME ' + srcDirectory + nameDirView + marginBorderSize + '>\n' +
      '  <FRAME ' + srcThumbnails + nameThumbView + marginBorderSize + '>\n' +
      ' </FRAMESET>\n' +
      ' <FRAMESET ROWS="50,*"' + frameBorderSize +  '>\n' +
      '  <FRAME' + srcNavView + nameNavView + marginBorderSize + ' SCROLLING="no" NORESIZE ' + '>\n';
    if( showReadme == 1 ) {
      result += '  <FRAME' + srcReadMe + nameImageView + marginBorderSize + '>\n';
    } else {
      result += '  <FRAME ' + srcImageView + nameImageView + marginBorderSize + '>\n';
    }
    result +=
      ' </FRAMESET>\n' +
      ' <FRAME' + srcHidden + nameHidden + ' SCROLLING="no" NORESIZE >\n' +
      '</FRAMESET>\n';
  } else if ( style == 3 ) {
    // Four frame screen with directories listed in left frame,
    // imagemap displayed in top-right frame, and README/Images
    // displayed in lower-right frame.
    //  -------------
    // |  |          |
    // |--+----------|
    // |--+----------|
    // |             |
    // |             |
    // |             |
    //  -------------
    result +=
      '<FRAMESET ROWS="190,50,*"' + frameBorderSize + '>\n' +
      ' <FRAMESET COLS="132,*,0"' + frameBorderSize + '>\n' +
      '  <FRAME ' + srcDirectory + nameDirView + marginBorderSize + '>\n' +
      '  <FRAME ' + srcThumbnails + nameThumbView + marginBorderSize + '>\n' +
      '  <FRAME ' + srcHidden + nameHidden + ' SCROLLING="no" NORESIZE >\n' +
      ' </FRAMESET>\n' +
      ' <FRAME' + srcNavView + nameNavView + marginBorderSize + ' SCROLLING="no" NORESIZE ' + '>\n';
    if( showReadme == 1 ) {
      result += '   <FRAME' + srcReadMe + nameImageView + marginBorderSize + '>\n';
    } else {
      result += '   <FRAME ' + srcImageView + nameImageView + marginBorderSize + '>\n';
    }
    result +=
      '</FRAMESET>\n';
  } else if ( style == 4 ) {
    // Four frame screen with imagemap displayed in top frame,
    // directories in left-center frame, image navigation icons in
    // right-center frame, and README/Images displayed in bottom
    // frame.
    //  -------------
    // |             |
    // |-------------|
    // |  |          |
    // |-------------|
    // |             |
    // |             |
    //  -------------
   result +=
     '<FRAMESET ROWS="172,50,*"' + frameBorderSize + '>\n' +
     ' <FRAME ' + srcThumbnails + nameThumbView + marginBorderSize + '>\n' +
     ' <FRAMESET COLS="132,*,0"' + frameBorderSize + '>\n' +
     '  <FRAME ' + srcDirectory + nameDirView + marginBorderSize + '>\n' +
     '  <FRAME ' + srcNavView + nameNavView + marginBorderSize + ' SCROLLING="no" NORESIZE ' + '>\n' +
     '  <FRAME ' + srcHidden + nameHidden + ' SCROLLING="no" NORESIZE >\n' +
     ' </FRAMESET>\n';
    if( showReadme == 1 ) {
      result += ' <FRAME ' + srcReadMe    + nameImageView + marginBorderSize + '>\n';
    } else {
      result += ' <FRAME ' + srcImageView + nameImageView + marginBorderSize + '>\n';
    }
    result +=
     '</FRAMESET>\n';
  }
  result += '</HTML>\n';
  //alert(result);
  return result;
}

///////////////////////////////////////////////////
//
// Routines and variables for the 'navview' frame
//
///////////////////////////////////////////////////
var navViewWindow;

function returnNavImageHTML()
{
    var result = '<CENTER>\n';
    // up arrow -- only displayed if three-frame style
    if( htmlOpts["framestyle"] == 1 ) {
	result +=
	    '<A HREF="javascript:parent.webmagick.displayThumbNails();"\n' +
	    '  onMouseOver="status=\'' + htmlOpts["msg_up"] + '\'; return true;"\n' +
	    '  onMouseOut="status=\'\';"\n' +
	    '  ><IMG SRC="' + iconImageUrls["up"] + '" ' + iconImageSizes["up"] +
	    ' ALT="' + htmlOpts["msg_up"] + '" BORDER=0></A>\n';
    }
    
    // if we have more than one image then display image nav buttons
    if(imageNames[pageNumber].length > 1 || imageNames.length > 1) {
	// preceding image arrow
	// if not first page or first page and not first image in page
	if( (pageNumber > 0) || (pageNumber == 0 && pageImageIndex > 0) ) {
	    result +=
		'<A HREF="javascript:parent.webmagick.displayPrecedingImage();"\n' +
		'  onMouseOver="status=\'' + htmlOpts["msg_prev"] + '\'; return true;"\n' +
		'  onMouseOut="status=\'\';"\n' +
		'  ><IMG SRC="' + iconImageUrls["prev"] + '" ' + iconImageSizes["prev"] +
		' ALT="' + htmlOpts["msg_prev"] + '" BORDER=0></A>\n';
	} else {
	    result +=
		'<IMG SRC="' + iconImageUrls["prev_gray"] + '" ' + iconImageSizes["prev_gray"] +
		' ALT="" BORDER=0></A>\n';
	}
	// next image arrow
	// if not last page or last page and not last image in page
	if( (pageNumber < imageNames.length - 1) ||
	    (pageNumber == imageNames.length - 1 &&
	     pageImageIndex < imageNames[pageNumber].length - 1 ) ) {
	    result +=
		'<A ' +
		'  HREF="javascript:parent.webmagick.displayNextImage();"\n' +
		'  onMouseOver="status=\''+ htmlOpts["msg_next"]+ '\'; return true;"\n' +
		'  onMouseOut="status=\'\';"\n' +
		'  ><IMG SRC="' + iconImageUrls["next"] + '" ' + iconImageSizes["next"] +
		' ALT="' + htmlOpts["msg_next"] + '" BORDER=0></A>\n';
	}else {
	    result +=
		'<IMG SRC="' + iconImageUrls["next_gray"] + '" ' + iconImageSizes["next_gray"] +
		' ALT="" BORDER=0></A>\n';
	}
    } // end of if we have one more image
    result += '</CENTER>\n';
    
    return result;
}

function returnNavThumbHTML()
{
    var result = '';
    
    // if no images then return empty page
    if( imageNames[pageNumber].length == 0 ) {
	return result;
    }

    if( htmlOpts["numpages"] > 1 ) {
	// start of table
	result += '<CENTER>\n<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=1>\n';
	
	// start navigation row
	result += '<TR ALIGN="center" VALIGN="top" NOWRAP>\n' ;
	
	result += '<TD NOWRAP>\n';
	// preceding page
	if( pageNumber == 0 ) {
	    // first page + no preceding
	    result +=
		'<IMG SRC="' + iconImageUrls["prev_gray"] + '" ' + iconImageSizes["prev_gray"] +
		' ALT="" BORDER=0></A>\n';
	} else {
	    // have preceding
	    result += '<A HREF="javascript:parent.webmagick.prevIndex();"\n' +
		'  TARGET="' + frameTargets["thumbview"] + '"\n' +
		'  onMouseOver="status=\'' + htmlOpts["msg_prev"] + '\'; return true;"\n' +
		'  onMouseOut="status=\'\';"\n' +
		'  ><IMG SRC="' + iconImageUrls["prev"] + '" ' + iconImageSizes["prev"] +
		' ALT="' + htmlOpts["msg_prev"] + '" BORDER=0></A>\n';
	}
	result += '</TD>\n';
	
	// select specific page
	result += '<TD>\n';
	result += '<FORM NAME="navcontrol">\n';
	result += '<FONT SIZE=1>\n';
	result += '<SELECT NAME="page"' +
	    ' onChange="parent.webmagick.pageNumber=document.navcontrol.page.selectedIndex;parent.webmagick.pageImageIndex=0;parent.webmagick.displayThumbNails();">\n';
	for( pageNum = 0; pageNum < htmlOpts["numpages"]; ++pageNum ) {
	    if( pageNum == pageNumber ) {
		result += '<OPTION VALUE="' + pageNum + '" SELECTED>' + (pageNum + 1) + '</OPTION>\n';
	    } else {
		result += '<OPTION VALUE="' + pageNum + '">' + (pageNum + 1) + '</OPTION>\n';
	    }
	}
	result += '</SELECT>\n';
	result += '<FONT SIZE=3>\n';
	result += '</FORM>\n';
	result += '</TD>\n';
	
	// next page
	result += '<TD>\n';
	if( pageNumber == htmlOpts["numpages"] - 1 ) {
	    // already on last page
	    result +=
		'<IMG SRC="' + iconImageUrls["next_gray"] + '" ' + iconImageSizes["next_gray"] +
		' ALT="" BORDER=0></A>\n';
	} else {
	    // more to go
	    result +=
		'<A HREF="javascript:parent.webmagick.nextIndex();"\n' +
		'  TARGET="' + frameTargets["thumbview"] + '"\n' +
		'  onMouseOver="status=\'' + htmlOpts["msg_next"] + '\'; return true;"\n' +
		'  onMouseOut="status=\'\';"\n' +
		'  ><IMG SRC="' + iconImageUrls["next"] + '" ' + iconImageSizes["next"] +
		' ALT="' + htmlOpts["msg_next"] + '" BORDER=0></A>\n';
	}
	result +=
	    '</TD>\n'+
	    '</TR>\n' +
	    '</TABLE>\n' +
	    '</CENTER>\n<BR>\n';
    } // End of code for multiple thumbnail pages

    return result;
}

function returnNavHTML()
{
    // page header
      var result =
    '<HTML>\n' +
    '<HEAD>\n' ;
    if (htmlOpts["stylesheet"])
    {
     result += '<LINK REL="stylesheet" type="text/css" HREF="' + htmlOpts["stylesheet"] + '">\n';
     result += '</HEAD>\n<BODY>\n';
    }
    else
    {
	    result += '</HEAD>\n' +
	    '<BODY\n' +
	    '    TEXT="' + htmlOpts["colorfore"] + '"\n' +
	    '    BGCOLOR="' + htmlOpts["colorback"] + '"\n';
	    if(iconImageUrls["background"]){
	    	result +='    BACKGROUND="' + iconImageUrls["background"] + '"\n';
	    	}
	    result +='    LINK="' + htmlOpts["colorlink"] + '"\n' +
	    '    VLINK="' + htmlOpts["colorvlink"] + '"\n' +
	    '    ALINK="' + htmlOpts["coloralink"] + '"\n' +
	    '>\n';
     }

 

    //alert('returnNavHTML():  thumbNailMode=' + thumbNailMode);

    if( htmlOpts["framestyle"] == 1 && thumbNailMode == true) {
	result += returnNavThumbHTML();
    } else {
	result += returnNavImageHTML();
    }

    result +=
	'</BODY>\n' +
	'</HTML>\n' ;
    
    return result;
}

///////////////////////////////////////////////////
//
// Routines and variables for the 'imageview' frame
//
///////////////////////////////////////////////////

// Image descriptions for image extensions
var imageExtensions = new Object();
imageExtensions['avs']  = 'AVS X image file';
imageExtensions['bie']  = 'Joint Bi-level Image experts Group file interchange format';
imageExtensions['bmp']  = 'Microsoft Windows bitmap image file';
imageExtensions['cgm']  = 'Computer Graphics Metafile';
imageExtensions['dcm']  = 'DICOM Medical image file';
imageExtensions['dcx']  = 'ZSoft IBM PC multi-page Paintbrush file';
imageExtensions['dib']  = 'Microsoft Windows bitmap image file';
imageExtensions['dot']  = 'Graphviz (DOT) file';
imageExtensions['dvi']  = 'TeX DVI file';
imageExtensions['epdf'] = 'Encapsulated Portable Document Format';
imageExtensions['epi']  = 'Adobe Encapsulated PostScript Interchange format';
imageExtensions['eps']  = 'Adobe Encapsulated PostScript file';
imageExtensions['eps2'] = 'Adobe Level II Encapsulated PostScript file';
imageExtensions['epsf'] = 'Adobe Encapsulated PostScript file';
imageExtensions['epsi'] = 'Adobe Encapsulated PostScript Interchange format';
imageExtensions['ept']  = 'Adobe Encapsulated PostScript with TIFF preview';
imageExtensions['fax']  = 'Group 3 FAX';
imageExtensions['fig']  = 'Xfig file';
imageExtensions['fits'] = 'Flexible Image Transport System';
imageExtensions['fpx']  = 'FlashPix Format';
imageExtensions['g3']   = 'Group 3 FAX';
imageExtensions['gif']  = 'CompuServe graphics interchange format';
imageExtensions['gplt'] = 'GNUPLOT plot file';
imageExtensions['hdf']  = 'Hierarchical Data Format';
imageExtensions['hpgl'] = 'HP-GL plotter file';
imageExtensions['ico']  = 'Microsoft icon';
imageExtensions['im1']  = 'SUN Rasterfile (1 bit)';
imageExtensions['im24'] = 'SUN Rasterfile (24 bit)';
imageExtensions['im8']  = 'SUN Rasterfile (8 bit)';
imageExtensions['jbg']  = 'Joint Bi-level Image experts Group file interchange format';
imageExtensions['jbig'] = 'Joint Bi-level Image experts Group file interchange format';
imageExtensions['jpeg'] = 'Joint Photographic Experts Group JFIF format';
imageExtensions['jpg']  = 'Joint Photographic Experts Group JFIF format';
imageExtensions['m2v']  = 'Motion Picture Experts Group file interchange format';
imageExtensions['man']  = 'Manual page';
imageExtensions['miff'] = 'Magick image file format';
imageExtensions['mng']  = 'Multiple-image Network Graphics';
imageExtensions['mpeg'] = 'Motion Picture Experts Group file interchange format';
imageExtensions['mpg']  = 'Motion Picture Experts Group file interchange format';
imageExtensions['mtv']  = 'MTV Raytracing image format';
imageExtensions['p7']   = 'Xv thumbnail format';
imageExtensions['pbm']  = 'Portable bitmap format (black and white)';
imageExtensions['pcd']  = 'Photo CD';
imageExtensions['pcds'] = 'Photo CD';
imageExtensions['pcx']  = 'ZSoft IBM PC Paintbrush file';
imageExtensions['pdf']  = 'Portable Document Format';
imageExtensions['pgm']  = 'Portable graymap format (gray scale)';
imageExtensions['pic']  = 'Apple Macintosh QuickDraw/PICT file';
imageExtensions['pict'] = 'Apple Macintosh QuickDraw/PICT file';
imageExtensions['pix']  = 'Alias/Wavefront RLE image format';
imageExtensions['png']  = 'Portable Network Graphics';
imageExtensions['pnm']  = 'Portable anymap';
imageExtensions['pov']  = 'Persistance Of Vision file';
imageExtensions['ppm']  = 'Portable pixmap format (color)';
imageExtensions['ps']   = 'Adobe PostScript file';
imageExtensions['psd']  = 'Adobe Photoshop bitmap file';
imageExtensions['rad']  = 'Radiance image format';
imageExtensions['rla']  = 'Alias/Wavefront image file';
imageExtensions['rle']  = 'Utah Run length encoded image file';
imageExtensions['sgi']  = 'Irix RGB image file';
imageExtensions['sun']  = 'SUN Rasterfile';
imageExtensions['tga']  = 'Truevision Targa image file';
imageExtensions['tif']  = 'Tagged Image File Format';
imageExtensions['tiff'] = 'Tagged Image File Format';
imageExtensions['tim']  = 'PSX TIM file';
imageExtensions['ttf']  = 'TrueType font file';
imageExtensions['vicar'] = 'VICAR rasterfile format';
imageExtensions['viff'] = 'Khoros Visualization image file';
imageExtensions['xbm']  = 'X Windows system bitmap (black and white)';
imageExtensions['xpm']  = 'X Windows system pixmap file (color)';
imageExtensions['xwd']  = 'X Windows system window dump file (color)';



// return HTML to display image with corresponding index
function returnImageHTML(imageIndex)
{
	
  pageImageIndex = imageIndex;

       var result =
    '<HTML>\n' +
    '<HEAD>\n' ;
    if (htmlOpts["stylesheet"])
    {
     result += '<LINK REL="stylesheet" type="text/css" HREF="' + htmlOpts["stylesheet"] + '">\n';
     result += '</HEAD>\n<BODY>\n';
    }
    else
    {
	    result += '</HEAD>\n' +
	    '<BODY\n' +
	    '    TEXT="' + htmlOpts["colorfore"] + '"\n' +
	    '    BGCOLOR="' + htmlOpts["colorback"] + '"\n';
	    if(iconImageUrls["background"]){
	    	result +='    BACKGROUND="' + iconImageUrls["background"] + '"\n';
	    	}
	    result +='    LINK="' + htmlOpts["colorlink"] + '"\n' +
	    '    VLINK="' + htmlOpts["colorvlink"] + '"\n' +
	    '    ALINK="' + htmlOpts["coloralink"] + '"\n' +
	    '>\n';
     }

	// only do work if image exists
	if (imageNames[pageNumber][imageIndex] != null) {
	
	  result += '  <P ALIGN="center"><small>';
	  // if image title defined, then use it, otherwise, image name 
	  if( imageLabels[imageNames[pageNumber][imageIndex]] != null ) {
	    result += imageLabels[imageNames[pageNumber][imageIndex]];
	  } else {
	    result += imageNames[pageNumber][imageIndex];
	  }
	  result +=
	    '</small></P>\n';
	
	  // Look for image file extension
	  var image = imageNames[pageNumber][imageIndex];
	  var delim = image.lastIndexOf('.');
	  var extension = '';
	  if ( delim > 0 ) {
	    extension = image.substring(delim + 1);
	    extension = extension.toLowerCase();
	  }
	
	  if( extension == 'gif' ||
	      extension == 'jpg' ||
	      extension == 'jpe' ||
	      extension == 'jpeg' ||
	      extension == 'xbm' ||
	      extension == 'png' ) {

	    // Browser supports displaying image in-line
	    result += '<P ALIGN="center"><IMG SRC="' + image + '" ALT=""></P>\n';
	  }
	  else {
	    // Browser requires an external helper program
	    result +=
	      '<P>Your browser may not have the capability to view files of format "' + imageExtensions[extension]
		+ '" as an in-line image. View image via <A HREF="' + image + '">a link</A> instead.</P>\n';
	  }
	  
	  // mod to get embedded mod files based on previews
	  var imageNoExtension = image.substr(0, image.length - (image.length - delim));
	  if (imageNoExtension.substr(imageNoExtension.length - 4, 4) == '.mov') {
		// it's a Quicktime movie
		result += '<CENTER><P><EMBED type="video/quicktime" src="' + imageNoExtension;
		// it would be nice if we had the size here
		result += '" PLUGINSPAGE="http://www.apple.com/quicktime/download/" width="240" height="196"></EMBED></P></CENTER>';
	  }
	  
	  if(htmlOpts["anonymous"] == 0) {
	    result +=
	      '<BR><HR>\n' +
	      '<ADDRESS><CENTER>Produced by ' +
	      '<A HREF="http://webmagick.sourceforge.net/" target="_top">WebMagick</A> ' +
	      htmlOpts["version"] + '\nCopyright &copy; Bob Friesenhahn</CENTER></ADDRESS>\n';
	  }
	}
  result +=
    '</BODY>\n' +
    '</HTML>\n';

  return result;
}

// Display current image in named window
// Display image
// Display thumbnails in navview window if framestyle != 1
var imageViewWindow;
function displayImage()
{
    //var targeturl = 'javascript:parent.webmagick.returnImageHTML(' + pageImageIndex + ');';
    //imageViewWindow = window.open( targeturl, frameTargets["imageview"]);
    displayResult(frameTargets["imageview"], returnImageHTML(pageImageIndex));
    
    if( htmlOpts["framestyle"] == 1) {
	thumbNailMode = false;
    }
    
    // alert('displayImage():  thumbNailMode=' + thumbNailMode);
    
    //var targeturl = 'javascript:parent.webmagick.returnNavHTML();';
    //navViewWindow = window.open( targeturl, frameTargets["navview"]);
    displayResult(frameTargets["navview"], returnNavHTML());
    
}

// Display an image by index number (setting current image)
function displayImageByIndex(imageIndex)
{
  pageImageIndex = imageIndex;
  displayImage();
}

// Display the next image in a series
function displayNextImage()
{
  // if not last image in current page, then increment index
  if(pageImageIndex < imageNames[pageNumber].length - 1 ) {
    ++pageImageIndex;
    displayImage();	// display new image
  }
  else {
    // if not last page then increment page and set index to zero
    if(pageNumber < imageNames.length - 1) {
      ++pageNumber;
      pageImageIndex = 0;
      if(htmlOpts["framestyle"] == 1) {
	displayImage();	// display new image
      } else {
	displayThumbNails();  // display new thumbnails & image
      }
    }
  }
}

// Display the preceding image in a series
function displayPrecedingImage()
{
  // if not first image in current page, then decrement index
  if( pageImageIndex > 0 ) {
    --pageImageIndex;
    displayImage();	// display new image
  }
  else {
    // if not first page, then decrement page and set index to max index for page
    if(pageNumber > 0) {
      --pageNumber;
      pageImageIndex = imageNames[pageNumber].length - 1;
      if(htmlOpts["framestyle"] == 1) {
	displayImage();	// display new image
      } else {
	displayThumbNails();  // display new thumbnails & image
      }
    }
  }
}


///////////////////////////////////////////////////
//
// Routines and variables for the 'thumbview' frame
//
///////////////////////////////////////////////////


// Write out thumbnail index HTML into thumbview window
// and write navigation info to navview window.

// Display thumbnails
// Display thumbnail navigator in thumbview window if framestyle=1
// Display image if framestyle!=1
var thumbViewWindow;

var thumbNailMode=true;
function displayThumbNails()
{
    //var targeturl = 'javascript:parent.webmagick.returnThumbNailsHTML();';
    //thumbViewWindow = window.open( targeturl, frameTargets["thumbview"]);
    displayResult(frameTargets["thumbview"], returnThumbNailsHTML());

    if ( htmlOpts["framestyle"] == 1 ) {
	thumbNailMode = true
	//var targeturl = 'javascript:parent.webmagick.returnNavHTML();';
	//navViewWindow = window.open( targeturl, frameTargets["navview"]);
	displayResult(frameTargets["navview"], returnNavHTML());
    } else {
	thumbNailMode = true
	displayImage();
    }
}

// return HTML to display thumbnail selection
function returnThumbNailsHTML()
{

  // page header
       var result =
    '<HTML>\n' +
    '<HEAD>\n' ;
    if (htmlOpts["stylesheet"])
    {
     result += '<LINK REL="stylesheet" type="text/css" HREF="' + htmlOpts["stylesheet"] + '">\n';
     result += '</HEAD>\n<BODY>\n';
    }
    else
    {
	    result += '</HEAD>\n' +
	    '<BODY\n' +
	    '    TEXT="' + htmlOpts["colorfore"] + '"\n' +
	    '    BGCOLOR="' + htmlOpts["colorback"] + '"\n';
	    if(iconImageUrls["background"]){
	    	result +='    BACKGROUND="' + iconImageUrls["background"] + '"\n';
	    	}
	    result +='    LINK="' + htmlOpts["colorlink"] + '"\n' +
	    '    VLINK="' + htmlOpts["colorvlink"] + '"\n' +
	    '    ALINK="' + htmlOpts["coloralink"] + '"\n' +
	    '>\n';
     }


  // if no images then return empty page
  if( imageNames[pageNumber].length == 0 ) {
    result +=
      '</BODY>\n' +
      '</HTML>\n' ;
    return result;
  }

  // If not in framestyle 1, display thumbnail navigator
  if ( htmlOpts["framestyle"] != 1 ) {
      result += returnNavThumbHTML();
  }

  result +=
    '<CENTER>\n';
    
  if ( htmlOpts["tables"] == 1) {  	
  	result += '<TABLE WIDTH=90%>';  	
  } else {
	  result +=
	    '<IMG SRC="' + montageImages[pageNumber] + '" ' +
	    montageImageSizes[pageNumber] +
	    ' USEMAP="#thumbnails" BORDER=0>' +
	    '<MAP NAME="thumbnails">\n';
  }
  for( imageNum = 0; imageNum < imageNames[pageNumber].length; ++imageNum ) {
  	if ( htmlOpts["tables"] == 1) {  	
  		if (imageNum%htmlOpts["columns"] == 0) {
  			result += '<TR ALIGN=CENTER VALIGN=TOP>';
  		}
  		result += '<TD><A HREF="javascript:parent.webmagick.displayImageByIndex(' + imageNum + ');"\n' +
  		  ' onMouseOver="status=\'View ' + escapeJs(imageNames[pageNumber][imageNum]) + '\'; return true;" \n' +
	      ' onMouseOut="status=\'\';">\n';
  		result += '<IMG BORDER=0 SRC="';
  		 // if thumb width is 0, then there must not be a cachefile (original image is small enough)
  		 if ( thumbwidths[pageNumber][imageNum] != 0) {
  		 	result += escapeURL(htmlOpts["cachedir"]) + '/';
  		 }
  		 result += escapeURL(imageNames[pageNumber][imageNum]) + '"';
  		 if ( thumbwidths[pageNumber][imageNum] != 0) {
  		 	result += ' HEIGHT=' + thumbheights[pageNumber][imageNum] + ' WIDTH= ' + thumbwidths[pageNumber][imageNum];
 		  }
 		 result += '></A><BR>\n'; 		  		  
  		if (imLabels[pageNumber][imageNum]) {	
  			result += '<FONT SIZE=-1>' + imLabels[pageNumber][imageNum] + '</FONT>\n';
  		}
  		result += '</TD>';
  		if (imageNum%htmlOpts["columns"] == (htmlOpts["columns"] - 1)) {
  			result += '</TR>';
  		}
  		 
  		
  	
} else {
	    result +=
	      '  <AREA HREF="javascript:parent.webmagick.displayImageByIndex(' + imageNum + ');"\n' +	    
	      ' onMouseOver="status=\'View ' + escapeJs(imageNames[pageNumber][imageNum]) + '\'; return true;"\n' +
	      ' onMouseOut="status=\'\';"\n' +
	      ' SHAPE=RECT COORDS="' + imageThumbCoords[pageNumber][imageNum] + '">\n';
	}
  }

  if ( htmlOpts["tables"] == 1) {
  	result += '</TABLE>';  	
  } else {
	  result +=
	    '</MAP>\n';
   }

  result += '</CENTER>\n<BR><ADDRESS>\n';
  if (htmlOpts["address"] != '') {
      result += htmlOpts["address"] + '<BR>\n';
  }
  result += '<FONT SIZE=-1>';
  result += htmlOpts["dateText"] + '\n';
  if(htmlOpts["anonymous"] == 0) {
    result +=
      '<BR><HR>\n' +
      htmlOpts["msg_produced_by"] + ' ' +
      '<NOBR><A HREF="http://webmagick.sourceforge.net/" target="_top">WebMagick</A> ' +
      htmlOpts["version"] + '</NOBR>, <NOBR>' + htmlOpts["msg_copyright"] + '&copy;</NOBR> <NOBR>Bob Friesenhahn</NOBR>\n';
  }
  result += '</FONT>\n</ADDRESS>\n';

  result +=
    '</BODY>\n' +
    '</HTML>\n' ;

  return result;
}

///////////////////////////////////////////////////
//
// Routines and variables for the 'dirview' frame
//
///////////////////////////////////////////////////

// Display directory listing in frame
var dirViewWindow;
function displayDirectory()
{
  displayResult(frameTargets["dirview"], returnDirectoryHTML());
}

var configWindow;
function saveConfig()
{	
	newColumns = configWindow.document.aForm.columns.value;
	newRows = configWindow.document.aForm.rows.value;
	var newStyle;
	for (var i = 0; i < configWindow.document.aForm.framestyle.length; i++)  {   
		if (configWindow.document.aForm.framestyle[i].checked) {         
			newStyle=configWindow.document.aForm.framestyle[i].value;
		}   
	}
	
	// TODO: error checking
	
	Set_Cookie('framestyle', newStyle, expires_date);
	Set_Cookie('columns', newColumns, expires_date);
	Set_Cookie('rows', newRows, expires_date);

	configWindow.close();
	parent.location.reload();
	
}

// display the window to allow configuration changes
function showConfig()
{
	configWindow = open('', 'wmConfig', 'toolbar=no,dependent=yes,menubar=no,status=no,resizable=yes,width=550,height=575');
	configWindow.document.open();
	result = '<HTML><HEAD><TITLE>Configure Gallery</TITLE></HEAD><BODY>';
	
	result += '<FORM NAME="aForm" ACTION="" ONSUBMIT="">';
	
	result += '<P>Columns: <INPUT WIDTH=5 TYPE="text" NAME="columns" VALUE="' + htmlOpts["columns"] + '">\n';
	result += ' Rows: <INPUT WIDTH=5 TYPE="text" NAME="rows" VALUE="' + htmlOpts["rows"] + '">\n';
	result += '<INPUT TYPE="reset" VALUE="Reset"><INPUT TYPE="button" VALUE="Save" onclick="window.opener.parent.webmagick.saveConfig()">';
	result += '<BR><TABLE BORDER=1><TR><TD><INPUT TYPE="radio" VALUE="1" NAME="framestyle"';
	if (htmlOpts["framestyle"] == 1) {
		result += ' checked ';
	}
	result += '><IMG SRC="' + iconImageUrls["frame-style-1"] + '" ' + iconImageSizes["frame-style-1"] + '></TD>\n';
	result += '<TD><INPUT TYPE="radio" VALUE="2" NAME="framestyle"';
	if (htmlOpts["framestyle"] == 2) {
		result += ' checked ';
	}
	result += '><IMG SRC="' + iconImageUrls["frame-style-2"] + '" ' + iconImageSizes["frame-style-2"] + '></TD></TR>\n';
	result += '<TR><TD><INPUT TYPE="radio" VALUE="3" NAME="framestyle"';
	if (htmlOpts["framestyle"] == 3) {
		result += ' checked ';
	}
	result += '><IMG SRC="' + iconImageUrls["frame-style-3"] + '" ' + iconImageSizes["frame-style-3"] + '></TD>\n';
	result += '<TD><INPUT TYPE="radio" VALUE="4" NAME="framestyle"';
	if (htmlOpts["framestyle"] == 4) {
		result += ' checked ';
	}
	result += '><IMG SRC="' + iconImageUrls["frame-style-4"] + '" ' + iconImageSizes["frame-style-4"] + '></TD></TR></TABLE>\n';
	result += '</FORM></BODY></HTML>';
	configWindow.document.write(result);
	configWindow.document.close();
	
}

// Navigate to directory
function changeDirectory(dirname)
{
  // Redisplay complete frameset
  top.window.location = escapeURL(dirname) + '/' + dirLinks[dirname];
}

// return HTML to display directory listing
function returnDirectoryHTML()
{
       var result =
    '<HTML>\n' +
    '<HEAD>\n' ;
    if (htmlOpts["stylesheet"])
    {
     result += '<LINK REL="stylesheet" type="text/css" HREF="' + htmlOpts["stylesheet"] + '">\n';
     result += '</HEAD>\n<BODY>\n';
    }
    else
    {
	    result += '</HEAD>\n' +
	    '<BODY\n' +
	    '    TEXT="' + htmlOpts["colorfore"] + '"\n' +
	    '    BGCOLOR="' + htmlOpts["colorback"] + '"\n';
	    if(iconImageUrls["background"]){
	    	result +='    BACKGROUND="' + iconImageUrls["background"] + '"\n';
	    	}
	    result +='    LINK="' + htmlOpts["colorlink"] + '"\n' +
	    '    VLINK="' + htmlOpts["colorvlink"] + '"\n' +
	    '    ALINK="' + htmlOpts["coloralink"] + '"\n' +
	    '>\n';
     }

    result +='<FONT SIZE=-1>\n';
  // up arrow
  result +=
    '<A HREF="javascript:parent.webmagick.changeDirectory(\'..\');"\n' +
    '  onMouseOver="status=\'' + htmlOpts["msg_up"] + '\'; return true;"\n' +
    '  onMouseOut="status=\'\';  return true;"\n' +
    '  ><IMG SRC="' + iconImageUrls["up"] + '" ' + iconImageSizes["up"] +
    ' ALT="' + htmlOpts["msg_up"] + '" BORDER=0></A>\n';
  
  // config
  if( htmlOpts["config"] && (htmlOpts["config"] != 0)) {
    result +=
      '<A HREF="javascript:parent.webmagick.showConfig();"\n' +
      '  onMouseOver="status=\'configure your frames\'; return true;"\n' +
      '  onMouseOut="status=\'\';"\n' +
      '  ><IMG SRC="' + iconImageUrls["config"] + '" ' + iconImageSizes["config"] +
      ' ALT="Config" BORDER=0></A>\n';
  }
  
  
  // help
  if( htmlOpts["readmepresent"] != 0 ) {
    result +=
      '<A HREF="' + htmlOpts["readme"] + '"\n' +
      '  TARGET="' + frameTargets["readmeview"] + '"\n' +
      '  onMouseOver="status=\'' + htmlOpts["msg_readme"] + '\'; return true;"\n' +
      '  onMouseOut="status=\'\';"\n' +
      '  ><IMG SRC="' + iconImageUrls["help"] + '" ' + iconImageSizes["help"] +
      ' ALT="' + htmlOpts["msg_readme"] + '" BORDER=0></A>\n';
  }
  
  
  
  // list out directory URLs
  if( dirNames.length != 0 ) {
    result += '<BR><B>' + htmlOpts["msg_directories"] + '</B><BR>\n';
    for( dirNum = 0; dirNum < dirNames.length; ++dirNum ) {
      
      // if we have directory title, then use it, else use directory name
      var dirTitle = '';
      if( dirTitles[dirNames[dirNum]] != null ) {
	dirTitle =  dirTitles[dirNames[dirNum]];
      } else {
	dirTitle = dirNames[dirNum];
      }
      result +=
	'<NOBR><A HREF="javascript:parent.webmagick.changeDirectory(\'' + escapeJs(dirNames[dirNum]) + '\');"\n' +
	'  onMouseOver="status=\'change to directory: ' + escapeJs(dirTitle) + '\'; return true;"\n' +
	'  onMouseOut="status=\'\';"\n';
      result += '>';
      result += dirTitle;
      result += '</A></NOBR><BR>\n';
    }
  }
  
  result += '</BODY></HTML>\n' ;
  
  return result;
}


///////////////////////////////////////////////////
//
// Routines and variables for the 'webmagick' frame
//
///////////////////////////////////////////////////

// display next index page
function nextIndex() {
  if( pageNumber < htmlOpts["numpages"] ) {
    ++pageNumber;
    pageImageIndex = 0;
    displayThumbNails();
  }
}

// display preceding index page
function prevIndex() {
  if( pageNumber > 0 ) {
    --pageNumber;
    pageImageIndex = 0;
    displayThumbNails();
  }
}

// Display specific index page
function goToIndex(number)
{
  pageNumber = number;
  pageImageIndex = 0;
  displayThumbNails();
}

///////////////////////////////////////////////////
//
// General purpose routines
//
///////////////////////////////////////////////////

// Escape characters in strings so they may be evaluated by JavaScript
function escapeJs(inString)
{
  var outString = '';
  //alert ('Inside escapeJs ' + inString);
  //if (! defined(inString)) { return outString; }
  for(pos = 0; pos < inString.length; pos++) {
    c = inString.charAt(pos);
    if(c == '\'' || c == '"' || c == '\n') {
      outString += '\\';
    }
    outString += c;
  }
  return outString;
}

// Escape spaces in URLs
function escapeURL(inString)
{
        var outString = '';
        for (pos = 0; pos < inString.length; pos++)
        {
                c = inString.charAt(pos);
                if (c == '%')
                {
                        outString += '%25';
                }
                else if (c == ' ')
                {
                        outString += '%20';
                }
                else if (c == '#')
                {
                	outString += '%23';
                }
                else
                {
                        outString += c;
                }
        }
        return outString;
}

function Get_Cookie(name) {
	
    var start = document.cookie.indexOf(name+"=");
    var len = start+name.length+1;
    if ((!start) && (name != document.cookie.substring(0,name.length))) return null;
    if (start == -1) return null;
    var end = document.cookie.indexOf(";",len);
    if (end == -1) end = document.cookie.length;
    return unescape(document.cookie.substring(len,end));
}

function Set_Cookie(name,value,expires,domain,secure) {
    document.cookie = name + "=" +escape(value) +
        ( (expires) ? ";expires=" + expires.toGMTString() : "") +
        ";path=" + htmlOpts["prefixpath"] + "/" + 
        ( (domain) ? ";domain=" + domain : "") +
        ( (secure) ? ";secure" : "");
               
}

function Delete_Cookie(name,path,domain) {
    if (Get_Cookie(name)) document.cookie = name + "=" +
        ( (path) ? ";path=" + path : "") +
        ( (domain) ? ";domain=" + domain : "") +
        ";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}

function fixDoubleArray(anArray, aRemainder) {
	
	var counter = 0;
	var perPage = htmlOpts["rows"] * htmlOpts["columns"];
	
	// create new array
	var tempArray = new Array(htmlOpts["numpages"]);
	for (i = 0; i < htmlOpts["numpages"]; i++) {
		if ((i == htmlOpts["numpages"] - 1) && (aRemainder != 0)) {
			tempArray[i] = new Array(aRemainder);
		} else {
			tempArray[i] = new Array(perPage);
		}		
	}
	
	// handle empty directory
	if (htmlOpts["numpages"] == 0) {
		tempArray = new Array(1);
		tempArray[0] = new Array(0);
	}
			
	for (i = 0; i < anArray.length; i++) {
		for (j = 0; j < anArray[i].length; j++) {			
			tempArray[(Math.floor(counter / perPage))][(counter % perPage)] = anArray[i][j];			
			counter++;
		}		
	}
	
	//alert ('tempArray ' + tempArray[0]);
	//anArray = tempArray;	
	return tempArray;
}

// indicate this file fully loaded
var WebMagickLoaded = 1;
