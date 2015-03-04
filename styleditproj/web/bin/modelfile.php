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
var $fieldNames;
var $styleTypes;
var $modelName;
var $organization;
 
	public function __construct($fname) {
		error_log ("ModelFile constructor, fname = " . $fname);
		$this->fileName = $fname;
	}
	
	/* Return field info for the model. The return value
	   is an array of two arrays:
	   0: The field names
	   1: The style types
	*/
	
	/* Load up the model information for the organization. */
	public function loadModelInfo($org) {
		error_log("getModelInfo, org = " . $org);
		if (!file_exists($this->fileName))
			return null;

		$this->fieldNames = array();
		$this->styleTypes = array();
		
		$simpleXML = simplexml_load_file($this->fileName);
		error_log ("returned from simplexml_load_file");
		// The field elements are one level down
		foreach ($simpleXML->children() as $elem) {
			if ($elem->getName() == "field") {
				foreach ($elem->attributes() as $attr => $val) {
					error_log ("Field attribute: " . $attr . "   value = " . $val);
					if ($attr == 'name')
						$this->fieldNames[] = $val;
				}
//				$this->fieldNames[] = trim(dom_import_simplexml($elem->name)->firstChild->data);
				$this->styleTypes[] = trim(dom_import_simplexml($elem->type)->firstChild->data);
			} else if ($elem->getName() == "org") {
				$this->organization = trim(dom_import_simplexml($elem)->firstChild->data);
				error_log ("Organization for model is " . $this->organization);
			}
		}
		// Find the name attribute
		foreach ($simpleXML->attributes() as $attr => $val) {
			error_log ("Checking attribute " . $attr);
			if ($attr == "name") {
				error_log ("Model name is " . $val);
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
 		error_log("findModel, model = " . $model . "   org = " . $org);
 		$path = XMLFile::CREATED_MODELS_DIR . $org . "/" . $category . "/" . $model . ".xml";
 		error_log("path = " . $path);
 		if (file_exists($path))
 			return new ModelFile($path);
 		return null;
 	}
 }