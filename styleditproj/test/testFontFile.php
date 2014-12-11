<?php

require_once('../web/bin/fontfile.php');

test1();
testAddContent();

function test1() {
	echo ("test1\n");
	$ff = new FontFile();
	if (!$ff)
		echo ("Creating FontFile object failed\n");
	echo ("test1 complete\n\n");
}

function testAddContent() {
	echo ("testAddContent\n");
	$ff = new FontFile();
	readFontFile ("../web/config/fonts.dat");
	$fonts = FontFile::$fonts;
	$err = false;
	if (!isset($fonts)) {
		$err = true;
		echo ("ERROR: No fonts array created\n");
	}
	if (!$err && sizeof($fonts) == 0) {
		$err = true;
		echo ("ERROR: Array fonts is empty");
	}
	
	if (!$err) {
		echo ("Fonts read:\n");
		while (list($key, $val) = each($fonts)) {
			echo ("$val\n");
		}
		echo ("\n");
	}
	echo ("testAddContent complete\n\n");

}