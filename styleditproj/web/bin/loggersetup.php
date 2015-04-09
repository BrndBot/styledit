<?php
/*
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2015
*/

/* Require this file in every PHP file that does logging. */

require ('vendor/autoload.php');
use Psr\Log\LogLevel;

date_default_timezone_set('America/New_York');

$logger = new Katzgrau\KLogger\Logger('/var/brndbot/logs', LogLevel::DEBUG);

function handlePHPErrors ($errno, $errstr) {
	global $logger;
	$logger->error ("Error reported, errno = " . $errno . ", errstr = " . $errstr);
}

set_error_handler ("handlePHPErrors");