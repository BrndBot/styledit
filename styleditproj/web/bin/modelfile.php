<?php
/* modelfile.php
 *
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
 */

require_once('xmlfile.php');

/* This class encapsulates some functions relating to
   model files. */
class ModelFile {
 
var $fileName;
 
	public function __construct($fname) {
		error_log ("ModelFile constructor, fname = " . $fname);
		$this->fileName = $fname;
	}
	
	/* Return field info for the model. The return value
	   is an array of two arrays:
	   0: The field names
	   1: The style types
	*/
	public function getModelInfo($org) {
		error_log("getModelInfo, org = " . $org);
		if (!file_exists($this->fileName))
			return null;

		$fieldNames = array();
		$styleTypes = array();
		
		$simpleXML = simplexml_load_file($this->fileName);
		error_log ("returned from simplexml_load_file");
		// The field elements are one level down
		foreach ($simpleXML->children() as $elem) {
			error_log ("Checking element " . $elem->getName());
			if ($elem->getName() == "field") {
				$fieldNames[] = trim(dom_import_simplexml($elem->name)->firstChild->data);
				$styleTypes[] = trim(dom_import_simplexml($elem->type)->firstChild->data);
			}
		}
		//error_log("fieldNames:");
		//ob_start();
		//var_dump($fieldNames);
		//error_log(ob_get_clean());
		return array ($fieldNames, $styleTypes);
	}
	
	/* Get the names of the model files for the named organization. */
 	public static function listModelFiles($org) {
 		$path = XMLFile::CREATED_MODELS_DIR . $org . "/";
 		$dirArray = scandir($path);
 		$val = array();
 		// Remove invisibles
 		for ($i = 0; $i < count($dirArray); $i++) {
 			$fname = trim($dirArray[$i]);
 			if (strpos($fname, ".") !== 0)
 				$val[] = $fname;
 		}
 		return $val;
 	}
 	
 	/* If the model file specified by the arguments exists,
 	   returns a ModelFile. Otherwise returns false. */
 	public static function findModel ($model, $org) {
 		error_log("findModel, model = " . $model . "   org = " . $org);
 		$path = XMLFile::CREATED_MODELS_DIR . $org . "/" . $model;
 		error_log("path = " . $path);
 		if (file_exists($path))
 			return new ModelFile($path);
 		return false;
 	}
 }