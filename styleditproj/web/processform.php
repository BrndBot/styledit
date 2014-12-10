<?php
/*
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
*/

require_once('bin/xmlfile.php');

error_reporting(E_WARNING);





/* Escape tag brackets for display purposes */
function escapeTags ($xml) {
	$xml1 = str_replace ('<', '&lt;', $xml);
	$xml2 = str_replace ('>', '&gt;', $xml1);
	return $xml2;
}

function buildFromForm () {
	$styleatt .= " name=" . '"' . $_POST["stylename"] . '"';
	$content = "";
	
	// Loop through all style segments by counting up the suffix
	// till we don't find a name with that suffix.
	for ($suffix = 0;; $suffix++) {
		$newSegment = buildSegment($suffix);
		error_log ("Returned segment " . $newSegment);
		if ($newSegment) {
			$content .= $newSegment;
		} else {
			break;
		}
	}
	error_log ("about to return final content " . $content);
	return XMLFile::wrapContentWithAtts ($content, "styleSet", $styleatt);
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
	error_log ("buildTextContent");
	$content = dimensionContent ($n);
	$defaultText = $_POST[appendSuffix("textcontent",$n)];
	if ($defaultText) {
		$content .= XMLFile::wrapContent ($defaultText, "default");
	if ($_POST["bold"]) {
		$content .= XMLFile::emptyTag("bold");
	}
	if ($_POST[appendSuffix("italic", $n)]) {
		$content .= XMLFile::emptyTag("italic");
	}
	return XMLFile::wrapContent ($content, "text");
		
	}
}

/* We have an SVG selection. Build its XML. */
function buildSVGContent ($n) {
	$content = dimensionContent ($n);
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
	error_log ("buildImageContent");
	$content = dimensionContent ($n);
	$content .= XMLFile::wrapContent ($_POST[appendSuffix("imagepath",$n)], "path");
	error_log ("content = " . $content);
	return XMLFile::wrapContent ($content, "image");	
}

/* We have a block selection. Build its XML. */
function buildBlockContent ($n) {
	$content = dimensionContent ($n);
	return XMLFile::wrapContent ($content, "block");	
}

/* We have a logo selection. Build its XML. */
function buildLogoContent ($n) {
	$content = dimensionContent ($n);
	return XMLFile::wrapContent ($content, "logo");	
}

/* Dimension content is common to all style types. */
function dimensionContent ($n) {
	error_log("dimensionContent");
	$wid = $_POST[appendSuffix("stylewidth", $n)];
	$ht = $_POST[appendSuffix("styleheight", $n)];
	$widElem = XMLFile::wrapContent ($wid, "x");
	$htElem = XMLFile::wrapContent ($ht, "y");
	return XMLFile::wrapContent ($widElem . $htElem, "dimensions");
}


/* Write the XML. This will fail if the file already exists. */
function saveXML ($xml) {
	try {
		$filename = $_POST["stylename"] . ".xml";
		$xmlf = new XMLFile($filename);
		$xmlf->writeFile($xml);
		return true;
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
	if (saveXML ($xml)) {
		echo ("<p>Form has been submitted successfully.</p>");
	}
?>
</pre>
<p><a href="enter.php">Enter another style</p>
</body>
