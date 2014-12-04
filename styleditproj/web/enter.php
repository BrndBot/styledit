<?php
/*	enter.php
	
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
*/
/*
require_once('bin/config.php');
require_once('bin/supportfuncs.php');
require_once('bin/globalconstants.php');
*/

header("Content-type: text/html; charset=utf-8");

session_start();
include('bin/sessioncheck.php');
if (!sessioncheck())
	return;

?>


<html lang="en">
<head>
	<title>Enter Style</title>
	<link href="css/styles.css" rel="stylesheet">
	
</head>
<body>
<noscript><strong>Sorry, JavaScript is required.</strong>
</noscript>

<?php

/*
$errparm = $_GET["err"];
if ($errparm != NULL) {
	switch ($errparm) {
	case IDFORM_NO_CLIPTYPE:
		$errmsg = "Please specify a clip type.";
		break;	
	case DB_INSERT_FAILURE:
		$errmsg = "Error writing to the database.";
		break;
	default:
		$errmsg = "Internal error.";
		break;
	}
	if ($errmsg)
		echo ("<p class='errormsg'>$errmsg</p>\n");
}
*/
?>
<form id="mainform" 
		action="processform.php" 
		method="post" 
		accept-charset="UTF-8">
Style set name: <input id="stylename" class="textbox" type="text" name="stylename" required>
</form>


<?
/* The following div is a bank from which form elements can be copied as needed for each
   style that's added. It is always hidden.
   
   The divs are identified by class, since they may be replicated.
*/
?>
<div id="formbank" class="hidden">

<div class="styletemplate">
<h1>Enter style information</h1>
<ul class="nobullet" title="Select the type of style">
<li><input type="radio" class="textstyle" 
		name="styletype" value="text"
		onclick="styleTypeUpdate(this);">
	<label for="textstyle">Text</label></li>
<li><input type="radio" class="svgstyle" 
		name="styletype" value="svg"
		onclick="styleTypeUpdate(this);">
	<label for="svgstyle">SVG</label></li>
<li><input type="radio" class="imagestyle" 
		name="styletype" value="image" 
		onclick="styleTypeUpdate(this);">
	<label for="imagestyle">Image</label></li>
<li>&nbsp;</li>
<li><input type="radio" class="blockstyle" 
		name="styletype" value="block" 
		onclick="styleTypeUpdate(this);">
	<label for="imagestyle">Image</label></li>
<li>&nbsp;</li>
<li><input type="radio" class="logostyle" 
		name="styletype" value="logo" 
		onclick="styleTypeUpdate(this);">
	<label for="imagestyle">Logo</label></li>
<li>&nbsp;</li>
</ul>
<div class="varinfo"></div>

	<button type="button" onclick="addStyle(this);">Add</button>
	<button type="button" onclick="removeStyle(this);">Remove</button>

<hr>
</div>	<!-- styletemplate -->


<div class="imageinfo">
<h4>Image</h4>
<ul class="nobullet">
	<li class="imagepath">
	Image file path: <input class="textbox" type="text" name="imagepath">
</ul>
</div>		<!-- imageinfo -->


<div id="svginfo" class="hidden">
<h4>SVG</h4>

<textarea name="svg" rows="4" cols="60" placeholder="<svg>...</svg>" >
</textarea>

<br>
Parameter(s): 
<ul class="nobullet">
	<li class="svgparamitem">
	Parameter name: <input class="paramnamebox" type="text" name="svgparamnames[]">
	Parameter value: <input class="paramvalbox" type="text" name="svgparamvalues[]">
	<button type="button" onclick="addSVGInput(this);">+</button>
	<button type="button" onclick="removeSVGInput(this);">-</button>
	</li>
	<li>&nbsp;</li>
</ul>


</div>	<!-- svginfo -->


<div class="textinfo">
<h4>Text</h4>
<ul class="nobullet">
	<li>
	Default content: <input class="textbox" type="text" name="textcontent">
	<li>
	<input id="boldcb" type="checkbox" name="bold">
	<label for="boldcb"><b>Bold</b></label>
	<li>
	<input id="italiccb" type="checkbox" name="italic">
	<label for="italiccb"><i>Italic</i></label>
</ul>

<div class="blockinfo">
<h4>Block</h4>
</div>		<!-- blockinfo -->

<div class="logoinfo">
<h4>Logo</h4>
</div>		<!-- logoinfo -->

<ul class="nobullet">
<li >
<input type="submit" class="submitbutton" value="Submit" ">
</li>
</ul>
</div>	<!-- End formbank -->





<!-- Put scripts at end for faster load -->

<script type="text/JavaScript"
	src="http://code.jquery.com/jquery-1.11.1.js"/>

<script type="text/javascript" src="js/enter.js"/>

</body>
</html>
