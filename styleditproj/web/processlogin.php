<?php
/*	login.php
	
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
 */

/* This PHP page is called when the user tries to log in.
   It has no HTML and always redirects.  */

require_once ('bin/supportfuncs.php');
require_once ('bin/orgfile.php');
require_once ('bin/fontfile.php');

	
try {
	$userName = trim(strip_tags($_POST["user"]));
	$pw = trim(strip_tags($_POST["pw"]));
	// Super-cheap login verification
	if ("print600" == $pw){
		session_start();
		$_SESSION['user'] = $userName;
		readOrganizationFile ('config/orgs.dat');
		$_SESSION['orgs'] = Organization::$organizations;
		
		readFontFile ("config/fonts.dat");
		$_SESSION['fonts'] = FontFile::$fonts;
		
		// org, brand, and promo are not set on login
		
		header ("Location: enter.php", true, 302);
		return;
	}
} catch (Exception $e) {
	error_log($e->getMessage());
}
error_log ("Login error for $userName");
header ("Location: login.php?error=1", true, 302);	// Should add an error message to login.php

?>
