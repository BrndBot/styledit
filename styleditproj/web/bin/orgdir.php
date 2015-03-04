<?php

/* orgdir.php
 *
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2015
 */
 
 /* This replaces orgfile.php with code to read the models directory
    and construct the organization directory from that rather
    than have a data file in the config directory.
 */
 
 class Category {
 	var $name;
 	var $models;	// Array of model names
 	
 	public static $selectedModel;
 	
	public function __construct($name) {
		$this->models = array();
		$this->name = $name;
	}
 }
 
 class Organization {
	var $name;
	var $brandIdentities;
	var $categories;	// array of Category
	var $modelPath;		// path to models directory
	var $stylePath;		// path to styles directory
	
	public static $organizations;
	public static $selectedOrg;
	public static $selectedBrand;
	public static $selectedCategory;
	
	/** Constructor.
	 *  Arguments:
	 *    Name of the organization
	 *    Base path with final slash (e.g., /var/brndbot/)
	 */
	public function __construct($name, $basePath) {
		if (!isset (Organization::$organizations)) {
			Organization::$organizations = array();
		}
		$this->name = $name;
		$this->modelPath = $basePath . '/models/' . $name . '/';
		$this->stylePath = $basePath . '/styles/' . $name . '/';
		$this->brandIdentities = array();
		$this->readStyleDir();
		$this->categories = array();
	}
	
	/** Returns the organization with the specified name, or null */
	public static function getByName ($nam) {
		reset (Organization::$organizations);
		while (list($key, $org) = each(Organization::$organizations)) {
			if ($org->name == $nam)
				return $org;
		}
		return null;
	}
	
//	public function addBrand($bname) {
//		$this->brandIdentities[] = $bname;
//	}
	
	/* Takes a category name as an argument and adds an empty Category object */
	public function addCategory($cname) {
		$cat = new Category($cname);
		$this->categories[] = $cat;
	}
	
	/* This function creates a div element holding option elements for
	   the organization's brand identities.
	   The id of the div is brand-[orgname] */
	public function insertBrandIdentities () {
		echo ("<div id='brand-" . $this->name . "'>\n");
//		reset($this->brandIdentities);
		foreach ($this->brandIdentities as $brand) {
			error_log ("Adding brand " . $brand);
			echo ("<option>" . $brand . "</option>\n"); 
		}
		echo ("</div>\n");
	}

	/* This function creates a div element holding option elements for
	   the organization's category types.
	   The id of the div is cat-[orgname] */
	public function insertCategories () {
		echo ("<div id='cat-" . $this->name . "'>\n");
		reset($this->categories);
		foreach ($this->categories as $cat) {
			echo ("<option>" . $cat->name . "</option>\n"); 
		}
		echo ("</div>\n");
	}
	
	/* This function creates a div element with the models for the
	 * named category.
	 */
	public function insertModels ($categoryName) {
		$cat = $this->getCategoryByName ($categoryName);
		// The div ID is model-orgname-catname
		echo ("<div id='model-" . $this->name . "-" . $categoryName . "' class='hidden'>\n");
		foreach ($cat->models as $model) {
			echo ("<option>" . $model . "</option>\n");
		}
		echo ("</div>\n");
	}

	/*  Reads the file names in the organization's model directory to
	 *  create an Organization object. The argument is the path to
	 *  the base directory (e.g., /var/brndbot), with a final slash.
 	 */
	public static function readModelDir ($basePath) {
		Organization::$organizations = array();
		$modelsPath = $basePath . 'models/';
		$dirArray = scandir($modelsPath);
		foreach ($dirArray as $orgName) {
 			if (substr($orgName, 0, 1) == '.')
 				continue;
 			$org = new Organization ($orgName, $basePath);
 			Organization::$organizations[] = $org;
 			
 			// Now find the categories for the organization
 			$catArray = scandir ($org->modelPath);
 			foreach ($catArray as $catName) {
 				if (substr($catName, 0, 1) == '.')
 					continue;
				if (strpos(strtolower($catName), '.png') > 0 ||
						strpos(strtolower($catName), '.jpg') > 0 ||
						strpos(strtolower($catName), '.gif') > 0)	// Don't list images
					continue;
 				$cat = new Category($catName);
	 			$org->categories[] = $cat;
	 			$catPath = $org->modelPath . $catName . "/";
	 			
	 			// Now get the models
	 			$modelArray = scandir ($catPath);
 				foreach ($modelArray as $modelName) {
 					// Strip off '.xml' and ignore non-XML files
 					$xmlPos = strpos($modelName, ".xml");
 					if ($xmlPos > 0) {
 						$modelName = substr($modelName, 0, $xmlPos);
 						error_log ("Truncated model name is " . $modelName);
 						$cat->models[] = $modelName;
 					}
 				}
 			}
 		}
	}
	
	/* Reads the directories in the organization's style directory to
	 * fill in the brand identities. The argument is the path to
	 * the styles directory, with a final slash.
	 */
	public function readStyleDir () {
		error_log ("readStyleDir");
		$dirArray = scandir($this->stylePath);
		foreach ($dirArray as $brand) {
			if (substr ($brand, 0, 1) == '.')
				continue;
			error_log ("brand " . $brand);
			$this->brandIdentities[] = $brand;
		}
	}
	
	/* Returns an array of the category directories */
	private function listCategoryDirs () {
 		$path = XMLFile::CREATED_MODELS_DIR . $this->name . "/";
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
	
	/* Returns the category object with the matching name */
	private function getCategoryByName ($catName) {
		foreach ($this->categories as $category) {
			if ($category->name == $catName)
				return $category;
		}
		error_log ("No category matching " . $catName);
		return $null;
	}
}
