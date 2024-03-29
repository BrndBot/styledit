<?php
/*
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
*/

require_once('bin/xmlfile.php');
require_once ('bin/orgdir.php');
require_once('bin/loggersetup.php');

/* Global to save the organization so we can set the
   appropriate file path. */
$g_org = "";
$g_category = "";

session_start ();

error_reporting(E_WARNING);

function buildFromForm () {
	global $logger;
	global $g_category;
	$logger->info ("buildFromForm");
	$modelatt .= " name=" . '"' . $_POST["modelname"] . '"';
	$g_category = $_POST["category"];
	$content = buildHeadContent();
	// Loop through all style segments by counting up the suffix
	// till we don't find a name with that suffix.
	//
	// RFC 2388 hedges on whether the order of the values will be
	// preserved. Assume it will for the moment; if it doesn't, come
	// up with a fix similar to what's done in the style editor.
	$fieldNames = $_POST["fieldname"];
	$styleTypes = $_POST["styletype"];
	for ($i = 0; $i < count($fieldNames); $i++) {
//		$nameElem = XMLFile::wrapContent ($fieldNames[$i], "name");
		$fieldAtt = " name=" . '"' . $fieldNames[$i] . '"';
		$styleElem = XMLFile::wrapContent ($styleTypes[$i], "type");
		$content .= XMLFile::wrapContentWithAtts ($nameElem . "\n" . $styleElem, "field", $fieldAtt);
	}
	return XMLFile::wrapContentWithAtts ($content, "model", $modelatt);
}

/* Build the one-time content that precedes the fields. */
function buildHeadContent() {
	global $g_org;
	global $g_category;
	$g_org = $_POST["orgname"];

	$content .= XMLFile::wrapContent ($g_org, "org");
	$content .= XMLFile::wrapContent ($_POST["description"], "description");
	$content .= XMLFile::wrapContent ($g_category, "category");
	return $content;
}


/* Write the XML. This will throw an exception if the file already 
   exists or anything else goes wrong. On success it returns the file name. */
function saveXML ($xml, $category) {
	global $logger;
	global $g_org;
	try {
		$logger->info ("saveXML");
//		if (!$g_org)
//			$g_org = "Crossfit";		// ***** TEMP *******
		$filename = $_POST["modelname"] . ".xml";
		$logger->info ("filename = " . $filename);
		$xmlf = new XMLFile($filename);
		$xmlf->writeFile($xmlf->makeModelPath($g_org, $category), $xml);
		$_SESSION['org'] = $g_org;
		return $filename;
	}
	catch (Exception $e) {
		$logger->error($e->getMessage());
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
	$logger->info ("xml = " . $xml);
	$filename = saveXML($xml, $g_category);
	if ($filename) {
		echo ("<p>Form has been submitted successfully, saved as " . $filename . ".</p>");
	}
	// Reload models to reflect changes
	Organization::readModelDir ("/var/brndbot/");
	$_SESSION['orgs'] = Organization::$organizations;	
?>

<p><a href="entermodel.php">Enter another model</a></p>