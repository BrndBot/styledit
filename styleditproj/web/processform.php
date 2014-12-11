<?php
/*
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
*/

require_once('bin/xmlfile.php');

error_reporting(E_WARNING);

/* Globals to save the organization, brand, and promo so we can set the
   appropriate file path. */
$g_org = "";
$g_brand = "";
$g_promo = "";



/* Escape tag brackets for display purposes */
function escapeTags ($xml) {
	$xml1 = str_replace ('<', '&lt;', $xml);
	$xml2 = str_replace ('>', '&gt;', $xml1);
	return $xml2;
}

function buildFromForm () {
	$styleatt .= " name=" . '"' . $_POST["stylename"] . '"';
	$content = buildHeadContent();
	
	// Loop through all style segments by counting up the suffix
	// till we don't find a name with that suffix.
	for ($suffix = 0;; $suffix++) {
		$newSegment = buildSegment($suffix);
		if ($newSegment) {
			$content .= $newSegment;
		} else {
			break;
		}
	}
	return XMLFile::wrapContentWithAtts ($content, "styleSet", $styleatt);
}

/* Build the one-time content that precedes the styles. */
function buildHeadContent() {
	global $g_org, $g_brand, $g_promo;
	// First the width and height of the whole piece
	$wid = $_POST["promowidth"];
	$ht = $_POST["promoheight"];
	$g_org = $_POST["orgname"];
	$g_brand = $_POST["brand"];
	$g_promo = $_POST["promo"];
	$widElem = XMLFile::wrapContent ($wid, "x");
	$htElem = XMLFile::wrapContent ($ht, "y");
	$content = XMLFile::wrapContent ($widElem . $htElem, "dimensions");

	// Then the organization, brand identity, and promotion.
	$content .= XMLFile::wrapContent ($g_org, "org");
	$content .= XMLFile::wrapContent ($g_brand, "brand");
	$content .= XMLFile::wrapContent ($g_promo, "promo");
	return $content;
}

/* Remove all white space from a name, so it makes a more friendly
   ID and directory name */
function whiteOut ($s) {
	return preg_replace('/\s+/', '', $s);
}

/* Need to grab stuff from this to put into a loop for each style segment */
function buildSegment ($n) {
	$styletype = $_POST[appendSuffix("styletype", $n)];
	if (!$styletype)
		return NULL;
	$content = "";
	switch ($styletype) {
		case "text":
			$content = buildTextContent($n);
			break;
		case "svg":
			$content = buildSVGContent($n);
			break;
		case "image":
			$content = buildImageContent($n);
			break;
		case "block":
			$content = buildBlockContent($n);
			break;
		case "logo":
			$content = buildLogoContent($n);
			break;
	}
	$styleatt = "name=" . '"' . $_POST["stylename"] . '"';
	return XMLFile::wrapContentWithAtts ($content, "style", $styleatt);
}

/* Append the suffix to a name */
function appendSuffix ($name, $n) {
	return $name . '-' . $n;
}

/* We have a text selection. Build its XML. */
function buildTextContent ($n) {
	$content = dimensionContent ($n);
	$content .= anchorContent ($n);
	$defaultText = $_POST[appendSuffix("textcontent",$n)];
	if ($defaultText) {
		$content .= XMLFile::wrapContent ($defaultText, "default");
	}
	$fontOption = $_POST[appendSuffix("font", $n)];
	$content .= XMLFile::wrapContent($fontOption, "font");
	$pointSize = $_POST[appendSuffix("pointsize", $n)];
	$content .= XMLFile::wrapContent($pointSize, "size");

	if ($_POST["bold"]) {
		$content .= XMLFile::emptyTag("bold");
	}
	if ($_POST[appendSuffix("italic", $n)]) {
		$content .= XMLFile::emptyTag("italic");
	}
	if ($_POST[appendSuffix("dropshadow", $n)]) {
		$h = $_POST[appendSuffix("dropshadh", $n)];
		$v = $_POST[appendSuffix("dropshadv", $n)];
		$blur = $_POST[appendSuffix("dropshadblur", $n)];
		$dropContent = XMLFile::wrapContent ($h, "h", 2);
		$dropContent .= XMLFile::wrapContent ($v, "v", 2);
		$dropContent .= XMLFile::wrapContent ($blur, "blur", 2);
		$content .= XMLFile::wrapContent ($dropContent, "dropshadow");
	}
	$paletteOption = $_POST[appendSuffix("palette", $n)];
	$content .= XMLFile::wrapContent($paletteOption, "palette");
	if ($paletteOption == 'palettecustom') {
		$customColor = $_POST[appendSuffix("textcolor", $n)];
		$content .= XMLFile::wrapContent ($customColor, "textcolor");
	}
	
	return XMLFile::wrapContent ($content, "text");
		
}

/* We have an SVG selection. Build its XML. */
function buildSVGContent ($n) {
	$content = dimensionContent ($n);
	$content .= anchorContent ($n);
	$svg = $_POST[appendSuffix("svg", $n)];
	$content .= $svg . "\n";
	$svgparamnames = $_POST[appendSuffix("svgparamnames", $n)];
	$svgparamvalues = $_POST[appendSuffix("svgparamvalues", $n)];
	for ($i = 0; $i < count($svgparamnames); $i++) {
		$pname = $svgparamnames[$i];
		$pval = $svgparamvalues[$i];
		$nameElement = XMLFile::wrapContent($pname, "name");
		$valElement = XMLFile::wrapContent($pval, "value");
		$content .= XMLFile::wrapContent ($nameElement . $valElement, "param");
		
	}
	return XMLFile::wrapContent ($content, "svgdata");
}

/* We have an image selection. Build its XML. */
function buildImageContent ($n) {
	$content = dimensionContent ($n);
	$content .= anchorContent ($n);
	$content .= XMLFile::wrapContent ($_POST[appendSuffix("imagepath",$n)], "path");
	$content .= XMLFile::wrapContent ($_POST[appendSuffix("opacity",$n)], "opacity");
	if ($_POST[appendSuffix("multiply", $n)]) {
		$content .= XMLFile::emptyTag("multiply");
	}
	
	return XMLFile::wrapContent ($content, "image");	
}

/* We have a block selection. Build its XML. */
function buildBlockContent ($n) {
	$content = dimensionContent ($n);
	$content .= anchorContent ($n);
	return XMLFile::wrapContent ($content, "block");	
}

/* We have a logo selection. Build its XML. */
function buildLogoContent ($n) {
	$content = dimensionContent ($n);
	$content .= anchorContent ($n);
	if ($_POST[appendSuffix("dropshadow", $n)]) {
		$h = $_POST[appendSuffix("logodropshadh", $n)];
		$v = $_POST[appendSuffix("logodropshadv", $n)];
		$blur = $_POST[appendSuffix("logodropshadblur", $n)];
		$dropContent = XMLFile::wrapContent ($h, "h", "2");
		$dropContent .= XMLFile::wrapContent ($v, "v", "2");
		$dropContent .= XMLFile::wrapContent ($blur, "blur", "2");
		$content .= XMLFile::wrapContent ($dropContent, "dropshadow");
	}
	return XMLFile::wrapContent ($content, "logo");	
}

/* Dimension content is common to all style types. */
function dimensionContent ($n) {
	$wid = $_POST[appendSuffix("stylewidth", $n)];
	$ht = $_POST[appendSuffix("styleheight", $n)];
	$widElem = XMLFile::wrapContent ($wid, "x");
	$htElem = XMLFile::wrapContent ($ht, "y");
	return XMLFile::wrapContent ($widElem . $htElem, "dimensions");
}

/* The anchor specification is common to all style types. */
function anchorContent ($n) {
	$anchor = $_POST[appendSuffix("anchor", $n)];
	switch ($anchor) {
		case "Top left":
			$a = "tl";
			break;
		case "Top right":
			$a = "tr";
			break;
		case "Bottom left":
			$a = "bl";
			break;
		case "Bottom right":
		default:
			$a = "br";
			break;
	}
	return XMLFile::wrapContent ($a, "anchor");	
}


/* Write the XML. This will throw an exception if the file already 
   exists or anything else goes wrong. On success it returns the file name. */
function saveXML ($xml) {
	global $g_org, $g_brand, $g_promo;
	try {
		$filename = $_POST["stylename"] . ".xml";
		$xmlf = new XMLFile($filename);
		$xmlf->writeFile($g_org, $g_brand, $g_promo, $xml);
		return $filename;
	}
	catch (Exception $e) {
		error_log($e->getMessage());
		echo ("<p>Could not save the file. <br>");
		echo ($e->getMessage() . "</p>");
		return false;
	}
}

?>

<html lang="en">
<head>
	<title>Thank you</title>
	<link href="css/styles.css" rel="stylesheet">
	
</head>
<body>


<?php
	$xml = buildFromForm ();
	$filename = saveXML($xml);
	if ($filename) {
		echo ("<p>Form has been submitted successfully, saved as " . $filename . ".</p>");
	}
?>
</pre>
<p><a href="enter.php">Enter another style</p>
</body>
