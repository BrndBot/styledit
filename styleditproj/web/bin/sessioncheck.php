<?php

/* sessioncheck.php
 *
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
 *
 *  Include this on every page that requires a logged-in user. 
 */

require_once ('bin/orgdir.php');
require_once ('bin/fontfile.php');

/* A convenient place to put this and make sure it will be called. */
date_default_timezone_set('America/New_York');

/* Automatically kick the user to the login page if no session with a user */
function sessioncheck () {
	if (!array_key_exists('user', $_SESSION)) {
		header ("Location: login.php", true, 302);
		return NULL;
	}
	else {
//		ob_start();
//		var_dump($_SESSION);
//		error_log(ob_get_clean());
		if (array_key_exists ('orgs', $_SESSION)) {
			Organization::$organizations = $_SESSION['orgs'];
		}
		if (array_key_exists ('fonts', $_SESSION)) {
			FontFile::$fonts = $_SESSION['fonts'];
		}
		if (array_key_exists ('org', $_SESSION)) {
			Organization::$selectedOrg = $_SESSION['org'];
		}
		if (array_key_exists ('brand', $_SESSION)) {
			Organization::$selectedBrand = $_SESSION['brand'];
		}
		if (array_key_exists ('channel', $_SESSION)) {
			Organization::$selectedChannel = $_SESSION['channel'];
		}
		return true;
	}
}

/* Return true if there is a session with a user */
function isSessionActive () {
	if (array_key_exists('user', $_SESSION)) 
		return true;
	return false;
}
?>