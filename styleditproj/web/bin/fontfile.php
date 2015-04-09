<?php
/* fontlist.php
 *
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
 */

require_once('bin/loggersetup.php');

/* This file contains the functions to read an font
   file and turn it into a font list.
   A font file is simply a list of font names, one per line.
   Blank lines are ignored.   
*/

class FontFile {

public static $fonts;

}

function readFontFile ($path) {
	global $logger;
	$logger->info("Reading font file $path");
	$fFile = fopen($path, "r");
	if (!$fFile) {
		$logger->error ("Could not open $path");
		FontFile::$fonts = null;
		return;
	}
	FontFile::$fonts = array();
	while (!feof($fFile)) {
		$line = fgets($fFile);
		if (!$line || !trim($line))
			continue;
		FontFile::$fonts[] = trim($line);
	}
	fclose($fFile);
}

