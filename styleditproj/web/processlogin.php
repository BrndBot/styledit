<?php
/*	login.php
	
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
 */

/* This PHP page is called when the user tries to log in.
   It has no HTML and always redirects.  */

require_once ('bin/supportfuncs.php');
require_once ('bin/orgdir.php');
require_once ('bin/fontfile.php');
require_once ('bin/loggersetup.php');

	
try {
	$userName = trim(strip_tags($_POST["user"]));
	$pw = trim(strip_tags($_POST["pw"]));
	// Super-cheap login verification
	if ("print600" == $pw){
		session_start();
		$_SESSION['user'] = $userName;
		Organization::readModelDir ("/var/brndbot/");
		$_SESSION['orgs'] = Organization::$organizations;
		
		readFontFile ("config/fonts.dat");
		if (!FontFile::$fonts) {
			http_respose_code(500);
			return;
		}
		$_SESSION['fonts'] = FontFile::$fonts;
		
		// org, brand, and promo are not set on login
		
		header ("Location: enter.php", true, 302);
		return;
	}
} catch (Exception $e) {
	$logger->info($e->getMessage());
}
$logger->info ("Login error for $userName");
header ("Location: login.php?error=1", true, 302);	// Should add an error message to login.php

?>
