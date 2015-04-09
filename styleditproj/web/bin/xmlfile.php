<?php
/* xmlfile.php
 *
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
 */

require_once('bin/loggersetup.php');

class XMLFile {

const CREATED_STYLES_DIR = '/var/brndbot/styles/';
const CREATED_MODELS_DIR = '/var/brndbot/models/';
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
	  *  This code assumes slash as a file separator and won't work on
	  *  Windows.
	  */
	public function writeFile ($dir, $content) {
		global $logger;
		$logger->info("writeFile to " . $dir);
		if (!isset($this->fileName)) {
			throw new Exception ('Invalid file name.');
		}
		$fPath = $dir . "/" . $this->fileName;
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
	
	/* Create directory path for a style. Returns the path without a slash at the end. */
	public function makeStylePath ($org, $brand) {
		// May need to create parent directories
		$path = XMLFile::CREATED_STYLES_DIR . $this->whiteOut($org);
		if (!file_exists ($path)) 
			mkdir($path);
		$path .= "/" . $this->whiteOut($brand);
		if (!file_exists ($path)) 
			mkdir($path);
//		$path .= "/" . $this->whiteOut($promo);
//		if (!file_exists ($path)) 
//			mkdir($path);
		return $path;
	}

	/* Create directory path for a model. Returns the path without a slash at the end. */
	public function makeModelPath ($org, $category) {
		// May need to create parent directory
		$path = XMLFile::CREATED_MODELS_DIR . 
			$this->whiteOut($org) .
			"/" .
			$this->whiteOut($category);
		if (!file_exists ($path)) 
			mkdir($path);
		return $path;
	}
	
	/* Remove all white space from a name, so it makes a more friendly
       ID and directory name */
	private function whiteOut ($s) {
		return preg_replace('/\s+/', '', $s);
	}
	
	
	
	/** Utility functions for building XML elements.
	 */
	 
	/* Wrap arbitrary content in a start and end tag, with an attributes string */
	public static function  wrapContentWithAtts ($content, $tag, $attrs) {
		if (strpos($content, "<") == 0) {
			$val = '<' . $tag . ' ' . $attrs . ">" .
				$content .
				"</" . $tag . ">\n";
		} else {
			$val =  '<' . $tag . ' ' . $attrs . ">\n" .
				$content .
				"\n</" . $tag . ">\n";
		}
		return $val;
	}
	
	/* Produce an empty tag */
	public static function emptyTag ($tag) {
		return '<' . $tag . "/>\n";
	}

	/* Wrap arbitrary content in a start and end tag.
	   Add line breaks around the content if it starts with "<". */
	public static function wrapContent ($content, $tag, $default=null) {
		if (!$content && isset ($default))
			$content = $default;
		if (strpos($content, "<") == 0) {
			$val = '<' . $tag . ">" .
				$content .
				"</" . $tag . ">\n";
		} else {
			$val = '<' . $tag . ">\n" .
				$content .
				"\n</" . $tag . ">\n";
		}
		return $val;
	}
	
	/* Parse an XML file */
	public function parse () {
		return simplexml_load_file($fileName);
	}
	
	/* An intruder can't do much, but snooping through the filesystem or smuggling
	   in a script in the filename is a possibility. */
	private static function isFilenameEvil ($fname) {
		// Argh. strpos may return either a number or false.
		$slashpos = strpos($fname, "/");
		$backslashpos = strpos($fname, "\\");
		$dotdotpos = strpos($fname, "..");
		$tildepos = strpos($fname, "~");
		$ltpos = strpos($fname, "<");
		return ($slashpos === 0 ||
			$backslashpos === 0 ||
			!($dotdotpos === false) ||
			!($ltpos === false) ||
			!($tildepos === false));
	}
}