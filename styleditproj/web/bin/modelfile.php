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
		$this->fileName = $fname;
	}
	
	/* Return field info for the model. The return value
	   is an array of two arrays:
	   0: The field names
	   1: The style types
	*/
	public function getModelInfo() {
		$path = XMLFile::CREATED_MODELS_DIR . $org . "/" . $fileName;
		if (!file_exists($path))
			return null;

		$fieldNames = array();
		$styleTypes = array();
		
		$simpleXML = simplexml_load_file($path);
		// The field elements are one level down
		foreach ($simpleXML->children() as $elem) {
			if ($elem->getName() == "field") {
				$fieldNames[] = $elem->name;
				$styleTypes[] = $elem->style;
			}
		}
		return array ($fieldNames, $styleTypes);
	}
	
 	public static function listModelFiles($org) {
 		$path = XMLFile::CREATED_MODELS_DIR . $org . "/";
 		return scandir($path);
 	}
 	
 	/* If the model file specified by the arguments exists,
 	   returns a ModelFile. Otherwise returns false. */
 	public static function findModel ($model, $org) {
 		$path = XMLFile::CREATED_MODELS_DIR . $org . "/" . $model;
 		if (file_exists($path))
 			return new ModelFile($path);
 		return false;
 	}
 }