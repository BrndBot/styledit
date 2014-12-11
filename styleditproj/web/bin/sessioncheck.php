<?php

/* sessioncheck.php
 *
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
 *
 *  Include this on every page that requires a logged-in user. 
 */

require_once ('bin/orgfile.php');

/* Automatically kick the user to the login page if no session with a user */
function sessioncheck () {
	if (!array_key_exists('user', $_SESSION)) {
		header ("Location: login.php", true, 302);
		return NULL;
	}
	else {
		if (array_key_exists ('orgs', $_SESSION)) {
			Organization::$organizations = $_SESSION['orgs'];
		}
		if (array_key_exists ('fonts', $_SESSION)) {
			FontFile::$fonts = $_SESSION['fonts'];
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