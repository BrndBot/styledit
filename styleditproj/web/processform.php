<?php
/*
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
*/

require_once('bin/xmlfile.php');
require_once('bin/orgdir.php');

session_start ();

//error_reporting(E_WARNING);

/* Globals to save the organization, brand, and promo so we can set the
   appropriate file path. */
$g_org = "";
$g_brand = "";
//$g_promo = "";



/* Escape tag brackets for display purposes */
function escapeTags ($xml) {
	$xml1 = str_replace ('<', '&lt;', $xml);
	$xml2 = str_replace ('>', '&gt;', $xml1);
	return $xml2;
}

function buildFromForm () {
	$styleatt .= " name=" . '"' . $_POST["stylename"] . '"';
	$content = buildHeadContent();
	
	// Loop through all style segments by counting up the suffix.
	// Can be gaps due to deleted segments! How do we set a terminating condition?
	// Setting a high limit is ugly but will do for now.
	for ($suffix = 0; $suffix <= 99; $suffix++) {
		$newSegment = buildSegment($suffix);
		if ($newSegment) {
			error_log ("newSegment = " . $newSegment);
			$content .= $newSegment;
		}
	}
	return XMLFile::wrapContentWithAtts ($content, "styleSet", $styleatt);
}

/* Build the one-time content that precedes the styles. */
function buildHeadContent() {
	global $g_org, $g_brand;
	// First the width and height of the whole piece
	$wid = $_POST["promowidth"];
	$ht = $_POST["promoheight"];
	$g_org = $_POST["orgname"];
	$g_brand = $_POST["brand"];
	
	$model = $_POST["model"];
	$content = XMLFile::wrapContent ($model, "model");
	
	$widElem = XMLFile::wrapContent ($wid, "x");
	$htElem = XMLFile::wrapContent ($ht, "y");
	$content .= XMLFile::wrapContent ($widElem . $htElem, "dimensions");

	// Then the organization, brand identity, and promotion.
	$content .= XMLFile::wrapContent ($g_org, "org");
	$content .= XMLFile::wrapContent ($g_brand, "brand");
//	$content .= XMLFile::wrapContent ($g_promo, "promo");
	return $content;
}

/* Remove all white space from a name, so it makes a more friendly
   ID and directory name */
function whiteOut ($s) {
	return preg_replace('/\s+/', '', $s);
}

/* Need to grab stuff from this to put into a loop for each style segment */
function buildSegment ($n) {
	if (!array_key_exists (appendSuffix("styletype", $n), $_POST)) {
		return NULL;
	}
	$styletype = $_POST[appendSuffix("styletype", $n)];
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
	if (array_key_exists (appendSuffix("fieldname", $n), $_POST)) {
		$fieldName = $_POST[appendSuffix("fieldname", $n)];
	}
	if ($fieldName)
		$retval = XMLFile::wrapContentWithAtts ($content, "style", 'field="' . $fieldName . '"');
	else {
		$retval = XMLFile::wrapContent ($content, "style");
	}
	return $retval;
}

/* Append the suffix to a name */
function appendSuffix ($name, $n) {
	return $name . '-' . $n;
}

/* We have a text selection. Build its XML. */
function buildTextContent ($n) {
	$content = buildCommonContent ($n, true);
	$defaultText = $_POST[appendSuffix("textcontent",$n)];
	if ($defaultText) {
		$content .= XMLFile::wrapContent ($defaultText, "default");
	}
	$fontOption = $_POST[appendSuffix("font", $n)];
	$content .= XMLFile::wrapContent($fontOption, "font");
	$alignOption = $_POST[appendSuffix("alignment", $n)];
	$content .= XMLFile::wrapContent($alignOption, "alignment");
	$pointSize = $_POST[appendSuffix("pointsize", $n)];
	$content .= XMLFile::wrapContent($pointSize, "size");

	if (array_key_exists(appendSuffix("bold", $n), $_POST)) {
		$content .= XMLFile::emptyTag("bold");
	}
	if (array_key_exists (appendSuffix("italic", $n), $_POST)) {
		$content .= XMLFile::emptyTag("italic");
	}
	if (array_key_exists (appendSuffix("dropshadow", $n), $_POST)) {
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

function buildCommonContent ($n, $useFieldName) {
	$content = '';
// 	if ($useFieldName) {
// 		$content .= fieldNameContent ($n);
// 	}
	$content .= dimensionContent ($n);
	$content .= anchorContent ($n);
	$content .= offsetContent ($n);
	$content .= hCenterContent ($n);
	return $content;
}


/* We have an SVG selection. Build its XML. */
function buildSVGContent ($n) {
	$content = buildCommonContent ($n, false);
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
	$content = buildCommonContent ($n, true);
	$content .= XMLFile::wrapContent ($_POST[appendSuffix("imagepath",$n)], "path");
	$content .= XMLFile::wrapContent ($_POST[appendSuffix("opacity",$n)], "opacity");
	if (array_key_exists(appendSuffix("multiply", $n), $_POST)) {
//	if ($_POST[appendSuffix("multiply", $n)]) {
		$content .= XMLFile::emptyTag("multiply");
	}
	
	return XMLFile::wrapContent ($content, "image");	
}

/* We have a block selection. Build its XML. */
function buildBlockContent ($n) {
	$content = buildCommonContent ($n, false);
	$paletteOption = $_POST[appendSuffix("palette", $n)];
	$content .= XMLFile::wrapContent($paletteOption, "palette");
	if ($paletteOption == 'palettecustom') {
		$customColor = $_POST[appendSuffix("blockcolor", $n)];
		$content .= XMLFile::wrapContent ($customColor, "blockcolor");
	}
	if (array_key_exists (appendSuffix("dropshadow", $n), $_POST)) {
		$h = $_POST[appendSuffix("blockdropshadh", $n)];
		$v = $_POST[appendSuffix("blockdropshadv", $n)];
		$blur = $_POST[appendSuffix("blockdropshadblur", $n)];
		$dropContent = XMLFile::wrapContent ($h, "h", "2");
		$dropContent .= XMLFile::wrapContent ($v, "v", "2");
		$dropContent .= XMLFile::wrapContent ($blur, "blur", "2");
		$content .= XMLFile::wrapContent ($dropContent, "dropshadow");
	}
	$content .= XMLFile::wrapContent ($_POST[appendSuffix("opacity",$n)], "opacity");
	if (array_key_exists (appendSuffix("multiply", $n), $_POST)) {
//	if ($_POST[appendSuffix("multiply", $n)]) {
		$content .= XMLFile::emptyTag("multiply");
	}
	return XMLFile::wrapContent ($content, "block");	
}

/* We have a logo selection. Build its XML. */
function buildLogoContent ($n) {
	$content = buildCommonContent ($n, false);
	if (array_key_exists (appendSuffix("dropshadow", $n), $_POST)) {
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

/* The field name is used in only some field types. */
function fieldNameContent ($n) {
	$fieldName = $_POST[appendSuffix("fieldname", $n)];
	return XMLFile::wrapContent ($fieldName, "field");
}


/* The anchor specification is common to all style types. */
function anchorContent ($n) {
	$anchor = $_POST[appendSuffix("anchor", $n)];
	switch ($anchor) {
		case "Top left":
		case "tl":
			$a = "tl";
			break;
		case "Top right":
		case "tr":
			$a = "tr";
			break;
		case "Bottom left":
		case "bl":
			$a = "bl";
			break;
		case "Bottom right":
		case "br":
		default:
			$a = "br";
			break;
	}
	return XMLFile::wrapContent ($a, "anchor");	
}

/* Offset content is common to all style types. */
function offsetContent ($n) {
	$h = $_POST[appendSuffix("hoffset", $n)];
	if (!$h)
		$h = "0";
	$v = $_POST[appendSuffix("voffset", $n)];
	if (!$v)
		$v = "0";
	$hElem = XMLFile::wrapContent ($h, "x");
	$vElem = XMLFile::wrapContent ($v, "y");
	return XMLFile::wrapContent ($hElem . $vElem, "offset");
}

/* hCenter is common to all style types. Return the
   hCenter element if the hcenter box was checked, otherwise 
   an empty string.
*/
function hCenterContent ($n) {
	$content = "";
	if (array_key_exists (appendSuffix("hcenter", $n), $_POST)) {
		$content .= XMLFile::emptyTag("hCenter");
	}
	return $content;
}


/* Write the XML to a file. This will throw an exception if the file already 
   exists or anything else goes wrong. On success it returns the file name. */
function saveXML ($xml) {
	global $g_org, $g_brand;
	try {
		$filename = $_POST["stylename"] . ".xml";
		$xmlf = new XMLFile($filename);
		$xmlf->writeFile($xmlf->makeStylePath($g_org, $g_brand), $xml);
		Organization::$selectedOrg = $g_org;
		$_SESSION['org'] = $g_org;
		$_SESSION['brand'] = $g_brand;
//		$_SESSION['promo'] = $g_promo;
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

<p><a href="enter.php">Enter another style</a></p>
</body>
