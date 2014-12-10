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
		if (array_key_exists ('organizations', $_SESSION)) {
			Organization::$organizations = $_SESSION['organizations'];
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