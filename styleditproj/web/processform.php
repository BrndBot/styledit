<?php

error_reporting(0);

/* Produce an empty tag */
function emptyTag ($tag) {
	return '<' . $tag . "/>\n";
}
/* Wrap arbitrary content in a start and end tag */
function wrapContent ($content, $tag) {
	return '<' . $tag . ">\n" .
		$content .
		"\n</" . $tag . ">\n";
}

/* Wrap arbitrary content in a start and end tag, with an attributes string */
function wrapContentWithAtts ($content, $tag, $attrs) {
	return '<' . $tag . ' ' . $attrs . ">\n" .
		$content .
		"\n</" . $tag . ">\n";
}

/* Escape tag brackets for display purposes */
function escapeTags ($xml) {
	$xml1 = str_replace ('<', '&lt;', $xml);
	$xml2 = str_replace ('>', '&gt;', $xml1);
	return $xml2;
}

function buildFromForm () {
	$styletype = $_POST["styletype"];
	$content = "";
	switch ($styletype) {
		case "text":
			$content = buildTextContent();
			break;
		case "svg":
			$content = buildSVGContent();
			break;
		case "image":
			$content = buildImageContent();
			break;
	}
	$styleatt = "type=" . '"' . $styletype . '"' ;
	return wrapContentWithAtts ($content, "style", $styleatt);
}

/* We have a text selection. Build its XML. */
function buildTextContent () {
	$content = "";
	$defaultText = $_POST["textcontent"];
	if ($defaultText) {
		$content .= wrapContent ($defaultText, "default");
	if ($_POST["bold"]) {
		$content .= emptyTag("bold");
	}
	if ($_POST["italic"]) {
		$content .= emptyTag("italic");
	}
	return wrapContent ($content, "text");
		
	}
}

function buildSVGContent () {
	$content = "";
	$svg = $_POST["svg"];
	$content .= $svg . "\n";
	$svgparamnames = $_POST["svgparamnames"];
	$svgparamvalues = $_POST["svgparamvalues"];
	for ($i = 0; $i < count($svgparamnames); $i++) {
		$pname = $svgparamnames[$i];
		$pval = $svgparamvalues[$i];
		$nameElement = wrapContent($pname, "name");
		$valElement = wrapContent($pval, "value");
		$content .= wrapContent ($nameElement . $valElement, "param");
		
	}
	return wrapContent ($content, "svgdata");
}

function buildImageContent () {
	$content = wrapcontent ($_POST["imagepath"], "path");
	return wrapContent ($content, "image");	
}
?>

<html lang="en">
<head>
	<title>Thank you</title>
	<link href="css/styles.css" rel="stylesheet">
	
</head>
<body>
<p>Form has been submitted</p>
<pre class="xmltext">
<?php
	echo (escapeTags(buildFromForm()));
?>
</pre>
<p><a href="enter.php">Enter another style</p>
</body>
