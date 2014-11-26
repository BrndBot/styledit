<?php
/*
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
*/


/* This function removes most HTML tags from text while allowing
   some basic formatting.
   To remove all tags, call strip_tags without a second argument.
   caller must include config.php (then we don't have to fuss with path-dependence)
    */
function strip_unsafe_html_tags( $text )
{
    return strip_tags( $text, "<p><b><i><em><strong><a><br>");
}


/* Dump a variable to the error log. */
function dumpVar ($v) {
	ob_start();
	var_dump($v);
	$contents = ob_get_contents();
	ob_end_clean();
	error_log($contents);
}




?>