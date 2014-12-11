<?php

require_once('../web/bin/xmlfile.php');

test1();
testWrap();
testWrapWithDefault();
testWrapWithDefault2();

/* Test that the include works */
function test1() {
	echo ("test1\n");
	$xf = new XMLFile ("test.xml");
	if ($xf->fileName != "test.xml")
		echo ("Could not get file name back\n");
	$xf = new XMLFile ("/etc/passwd");
	if ($xf->fileName) 
		echo ("Failed to block evil filename " . $xf->fileName . "\n");
	$xf = new XMLFile ("\\etc\\passwd");
	if ($xf->fileName) 
		echo ("Failed to block evil filename " . $xf->fileName . "\n");
	$xf = new XMLFile ("../../bin/evil");
	if ($xf->fileName) 
		echo ("Failed to block evil filename " . $xf->fileName . "\n");
	echo ("test1 complete\n\n");
}

/* Test wrap */
function testWrap () {
	echo ("testWrap\n");
	$elem = XMLFile::wrapContent ("3", "a");
	echo ("wrapContent generated " . $elem);
	if (strpos($elem, "3") === false)
		echo ("ERROR: missing content\n");
	if (strpos($elem, "<a>") === false)
		echo ("ERROR: missing open tag\n");
	if (strpos($elem, "</a>") === false)
		echo ("ERROR: missing close tag\n");
	if (!(strpos($elem, "<aaaaa>") === false))
		echo ("ERROR: malfunctioning test\n");
	echo ("testWrap complete\n\n");
}

/* Test wrap with default value overriding null */
function testWrapWithDefault () {
	echo ("testWrapWithDefault\n");
	$elem = XMLFile::wrapContent (null, "b", "56");
	echo ("wrapContent generated " . $elem);
	if (strpos($elem, "56") === false)
		echo ("ERROR: default not successfully applied\n");
	if (strpos($elem, "<b>") === false)
		echo ("ERROR: missing open tag\n");
	echo ("testWrapWithDefault complete\n\n");
}

/* Test wrap with default value overriding blank */
function testWrapWithDefault2 () {
	echo ("testWrapWithDefault2\n");
	$elem = XMLFile::wrapContent ("", "b", "56");
	echo ("wrapContent generated " . $elem);
	if (strpos($elem, "56") === false)
		echo ("ERROR: default not successfully applied\n");
	if (strpos($elem, "<b>") === false)
		echo ("ERROR: missing open tag\n");
	echo ("testWrapWithDefault2 complete\n\n");
}

