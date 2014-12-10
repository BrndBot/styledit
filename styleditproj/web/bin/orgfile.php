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
	
	public function __construct($name) {
		if (!isset (Organization::$organizations)) {
			Organization::$organizations = array();
		}
		$this->name = $name;
		$this->brandIdentities = array();
		$this->promotions = array();
	}
	
	public function addBrand($bname) {
		$brandIdentities[] = $bname;
	}
	
	public function addPromotion($pname) {
		$promotions[] = $pname;
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
	}
	fclose($orgFile);
}
?>

