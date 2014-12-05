<?php

/* sessioncheck.php

 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014

   Include this on every page that requires a logged-in user.
   It turns out that "return" from here merely returns to the
   calling script, so the caller has to return again or the
   whole page will be executed. The caller has to return
   if sessioncheck returns false. */

require_once ('bin/orgfile.php');

/* Automatically kick the user to the login page if no session with a user */
function sessioncheck () {
	if (!$_SESSION['user']) {
		header ("Location: login.php", true, 302);
		return NULL;
	}
	else {
		Organization::$organizations = $_SESSION['organizations'];
		return true;
	}
}

/* Return true if there is a session with a user */
function isSessionActive () {
	if ($_SESSION['user']) 
		return true;
	return false;
}
?>