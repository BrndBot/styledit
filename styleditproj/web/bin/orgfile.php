<?php
/* orgfile.php
 *
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
 */

/* This file contains the functions to read an organization
   description file and turn it into an organization structure.
   An organization description file looks something like this:

organization:CrossFit
brand:Shocking
brand:Subdued
promotion:Teacher
promotion:Workshop

organization:MindBody
brand:Weird
promotion:Class

Lines without a colon are ignored. The first line with a colon must
be an organization.
   
*/

class Organization {
	var $name;
	var $brandIdentities;
	var $promotions;
	
	public static $organizations;
	public static $selectedOrg;
	public static $selectedBrand;
	public static $selectedPromo;
	
	public function __construct($name) {
		if (!isset (Organization::$organizations)) {
			Organization::$organizations = array();
		}
		$this->name = $name;
		$this->brandIdentities = array();
		$this->promotions = array();
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
	
	public function addBrand($bname) {
		$this->brandIdentities[] = $bname;
	}
	
	public function addPromotion($pname) {
		$this->promotions[] = $pname;
	}
	
	/* This function creates a div element holding option elements for
	   the organization's brand identities.
	   The id of the div is brand-[orgname] */
	public function insertBrandIdentities () {
		echo ("<div id='brand-" . $this->name . "'>\n");
		reset($this->brandIdentities);
		while (list($key, $brand) = each($this->brandIdentities)) {
			echo ("<option>" . $brand . "</option>\n"); 
		}
		echo ("</div>\n");
	}

	/* This function creates a div element holding option elements for
	   the organization's promotion types.
	   The id of the div is promo-[orgname] */
	public function insertPromotions () {
		echo ("<div id='promo-" . $this->name . "'>\n");
		reset($this->promotions);
		while (list($key, $promo) = each($this->promotions)) {
			echo ("<option>" . $promo . "</option>\n"); 
		}
		echo ("</div>\n");
	}

}

function readOrganizationFile ($path) {
	$currentOrg = null;
	$organizations = array();
	$orgFile = fopen($path, "r");
	while (!feof($orgFile)) {
		$line = fgets($orgFile);
		$parsedLine = explode(":", $line);
		if (count($parsedLine) < 2)
			continue;			// No colon in line, skip
		$lineType = trim($parsedLine[0]);
		$lineVal = trim($parsedLine[1]);
		if ($lineType == 'organization') {
			$currentOrg = new Organization($lineVal);
			Organization::$organizations[] = $currentOrg;
		}
		else if ($lineType == 'brand') {
			$currentOrg->addBrand($lineVal);
		}
		else if ($lineType == 'promotion') {
			$currentOrg->addPromotion($lineVal);
		}
//		ob_start();
//		var_dump($currentOrg);
//		error_log(ob_get_clean());
	}
	fclose($orgFile);
}
?>

