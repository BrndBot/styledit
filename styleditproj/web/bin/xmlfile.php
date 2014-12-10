<?php
/* orgfile.php
 *
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
 */

class XMLFile {

const CREATED_STYLES_DIR = '/var/brndbot/styles/';
const XML_DECL = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

var $fileName;

	public function __construct($fname) {
		if (XMLFile::isFilenameEvil($fname)) {
			$this->fileName = null;
		}
		else {
			$this->fileName = $fname;
		}
	}
	
	/** Write XML content to a file. Adds the XML declaration at the top.
	  *  Will not overwrite an existing file. May throw an exception.
	  */
	public function writeFile ($content) {
		if (!isset($this->fileName)) {
			throw new Exception ('Invalid file name.');
		}
		$fPath = XMLFile::CREATED_STYLES_DIR . $this->fileName;
		error_log ("Writing " . $fPath);
		if (file_exists($fPath)) {
			throw new Exception ('File already exists.');
		}
		$xmlfile = fopen ($fPath, "x");
		if (!$xmlfile) {
			throw new Exception ('Error creating file.');
		}
		try {
			fwrite ($xmlfile, XMLFile::XML_DECL);
			fwrite ($xmlfile, $content);
		} catch (Exception $e) {
			fclose($xmlfile);
			throw $e;
		}
		fclose ($xmlfile);
	}
	
	/** Utility function for building XML elements.
	 */
	 
	/* Wrap arbitrary content in a start and end tag, with an attributes string */
	public static function  wrapContentWithAtts ($content, $tag, $attrs) {
	return '<' . $tag . ' ' . $attrs . ">\n" .
		$content .
		"\n</" . $tag . ">\n";
	}
	
	/* Produce an empty tag */
	public static function emptyTag ($tag) {
		return '<' . $tag . "/>\n";
	}

	/* Wrap arbitrary content in a start and end tag */
	public static function wrapContent ($content, $tag) {
		error_log ("XMLFile wrapContent " . $content);
		error_log ("tag = " . $tag);
		return '<' . $tag . ">\n" .
			$content .
			"\n</" . $tag . ">\n";
	}
	
	/* An intruder can't do much, but snooping through the filesystem is a possibility. */
	private static function isFilenameEvil ($fname) {
		// Argh. strpos may return either a number or false.
		error_log("Checking filename " . $fname);
		$slashpos = strpos($fname, "/");
		$backslashpos = strpos($fname, "\\");
		$dotdotpos = strpos($fname, "..");
		$tildepos = strpos($fname, "~");
		return (($slashpos && $slashpos == 0) ||
			($backslashpos && $backslashpos == 0) ||
			$dotdotpos ||
			$tildepos);
	}
}