<?php
/*
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
*/

require_once('bin/loggersetup.php');

/* This function removes most HTML tags from text while allowing
   some basic formatting.
   To remove all tags, call strip_tags without a second argument.
   caller must include config.php (then we don't have to fuss with path-dependence)
    */
function strip_unsafe_html_tags( $text )
{
    return strip_tags( $text, "<p><b><i><em><strong><a><br>");
}

/* Replace spaces in a string with tildes. Needed when a category or model name
   is used in an HTML ID or other attribute. */
function spaceToUnderscore ($s) {
	return str_replace (array(" "), "_", $s);
}


/* Dump a variable to the error log. */
function dumpVar ($v) {
	global $logger;
	ob_start();
	var_dump($v);
	$contents = ob_get_contents();
	ob_end_clean();
	$logger->info($contents);
}




?>