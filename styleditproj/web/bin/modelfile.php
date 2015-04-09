<?php
/* modelfile.php
 *
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
 */

require_once('xmlfile.php');
require_once('bin/loggersetup.php');


/* This class encapsulates some functions relating to
   model files. */
class ModelFile {
 
var $fileName;
var $fieldNames;
var $styleTypes;
var $modelName;
var $organization;
 
	public function __construct($fname) {
		global $logger;
		$logger->info ("ModelFile constructor, fname = " . $fname);
		$this->fileName = $fname;
	}
	
	/* Return field info for the model. The return value
	   is an array of two arrays:
	   0: The field names
	   1: The style types
	*/
	
	/* Load up the model information for the organization. */
	public function loadModelInfo($org) {
		global $logger;
		$logger->info("getModelInfo, org = " . $org);
		if (!file_exists($this->fileName))
			return null;

		$this->fieldNames = array();
		$this->styleTypes = array();
		
		$simpleXML = simplexml_load_file($this->fileName);
		if (!$simpleXML) {
			$logger->error ("Could not open model file " . $this->fileName);
			return null;		
		}
		// The field elements are one level down
		foreach ($simpleXML->children() as $elem) {
			if ($elem->getName() == "field") {
				foreach ($elem->attributes() as $attr => $val) {
					if ($attr == 'name')
						$this->fieldNames[] = $val;
				}
//				$this->fieldNames[] = trim(dom_import_simplexml($elem->name)->firstChild->data);
				$this->styleTypes[] = trim(dom_import_simplexml($elem->type)->firstChild->data);
			} else if ($elem->getName() == "org") {
				$this->organization = trim(dom_import_simplexml($elem)->firstChild->data);
			}
		}
		// Find the name attribute
		foreach ($simpleXML->attributes() as $attr => $val) {
			if ($attr == "name") {
				$this->modelName = $val;
			}
		}
		//ob_start();
		//var_dump($fieldNames);
		//error_log(ob_get_clean());
		//return array ($fieldNames, $styleTypes);
	}
	
	/* Get the names of the model files for the named category. */
 	public static function listModelFiles($category, $org) {
 		$path = XMLFile::CREATED_MODELS_DIR . $org . "/" . $category . "/";
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
 	   returns a ModelFile. Otherwise returns null. */
 	public static function findModel ($model, $category, $org) {
 		global $logger;
 		$logger->info("findModel, model = " . $model . "   org = " . $org);
 		$path = XMLFile::CREATED_MODELS_DIR . $org . "/" . $category . "/" . $model . ".xml";
 		if (file_exists($path))
 			return new ModelFile($path);
 		return null;
 	}
 }