<?php
/*
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
*/

require_once('bin/xmlfile.php');

/* Global to save the organization so we can set the
   appropriate file path. */
$g_org = "";

session_start ();

error_reporting(E_WARNING);

function buildFromForm () {
	error_log ("buildFromForm");
	$modelatt .= " name=" . '"' . $_POST["modelname"] . '"';
	$content = buildHeadContent();
	error_log ("Head = " . $content);
	// Loop through all style segments by counting up the suffix
	// till we don't find a name with that suffix.
	//
	// RFC 2388 hedges on whether the order of the values will be
	// preserved. Assume it will for the moment; if it doesn't, come
	// up with a fix similar to what's done in the style editor.
	$fieldNames = $_POST["fieldname"];
	$styleTypes = $_POST["styletype"];
	error_log ("Number of fields: " . count($fieldNames));
	for ($i = 0; $i < count($fieldNames); $i++) {
		error_log ("Processing field " . $i);
		$nameElem = XMLFile::wrapContent ($fieldNames[$i], "name");
		error_log("Name: " . $nameElem);
		$styleElem = XMLFile::wrapContent ($styleTypes[$i], "type");
		$content .= XMLFile::wrapContent ($nameElem . "\n" . $styleElem, "field");
		error_log ("Content with field = "  . $content);
	}
	return XMLFile::wrapContentWithAtts ($content, "model", $modelatt);
}

/* Build the one-time content that precedes the fields. */
function buildHeadContent() {
	global $g_org;
	$g_org = $_POST["orgname"];

	$content .= XMLFile::wrapContent ($g_org, "org");
	$content .= XMLFile::wrapContent ($_POST["description"], "description");
	$content .= XMLFile::wrapContent ($_POST["category"], "category");
	return $content;
}


/* Write the XML. This will throw an exception if the file already 
   exists or anything else goes wrong. On success it returns the file name. */
function saveXML ($xml) {
	global $g_org;
	try {
		error_log ("saveXML");
		if (!$g_org)
			$g_org = "Crossfit";		// ***** TEMP *******
		$filename = $_POST["modelname"] . ".xml";
		error_log ("filename = " . $filename);
		$xmlf = new XMLFile($filename);
		$xmlf->writeFile($xmlf->makeModelPath($g_org), $xml);
		$_SESSION['org'] = $g_org;
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
	error_log ("xml = " . $xml);
	$filename = saveXML($xml);
	if ($filename) {
		echo ("<p>Form has been submitted successfully, saved as " . $filename . ".</p>");
	}
?>

<p><a href="entermodel.php">Enter another model</a></p>