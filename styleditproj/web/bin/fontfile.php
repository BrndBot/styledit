<?php
/* fontlist.php
 *
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
 */

/* This file contains the functions to read an font
   file and turn it into a font list.
   A font file is simply a list of font names, one per line.
   Blank lines are ignored.   
*/

class FontFile {

public static $fonts;

}

function readFontFile ($path) {
	error_log("Reading font file $path");
	FontFile::$fonts = array();
	$fFile = fopen($path, "r");
	while (!feof($fFile)) {
		$line = fgets($fFile);
		if (!$line || !trim($line))
			continue;
		error_log("Adding font $line");
		FontFile::$fonts[] = trim($line);
	}
	fclose($fFile);
}

