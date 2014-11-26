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
<form action="processform.php" method="post" accept-charset="UTF-8">
<h1>Enter style information</h1>
<ul class="nobullet" title="Select the type of style">
<li><input type="radio" id="textstyle" 
		name="styletype" value="text"
		onclick="styleTypeUpdate();">
	<label for="textstyle">Text</label></li>
<li><input type="radio" id="svgstyle" 
		name="styletype" value="svg"
		onclick="styleTypeUpdate();">
	<label for="svgstyle">SVG</label></li>
<li><input type="radio" id="imagestyle" 
		name="styletype" value="image" 
		onclick="styleTypeUpdate();">
	<label for="imagestyle">Image</label></li>
<li>&nbsp;</li>
</ul>

Style name: <input id="stylename" class="textbox" type="text" name="stylename" required>

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
	<button type="button" onclick="addsvginput(this);">+</button>
	<button type="button" onclick="removesvginput(this);">-</button>
	</li>
	<li>&nbsp;</li>
</ul>


</div>	<!-- svginfo -->



<div id="imageinfo" class="hidden">
<h4>Image</h4>
<ul class="nobullet">
	<li class="imagepath">
	Image file path: <input class="textbox" type="text" name="imagepath">
</ul>
</div>		<!-- imageinfo -->


<div id="textinfo" class="hidden">
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
</div>		<!-- textinfo -->

<ul class="nobullet">
<li >
<input type="submit" class="submitbutton" value="Submit" ">
</li>
</ul>



</form>



<!-- Put scripts at end for faster load -->

<script type="text/JavaScript"
src="http://code.jquery.com/jquery-1.11.1.js">
</script>
<script type="text/JavaScript">
$(document).ready(
	function () {
		styleTypeUpdate();
		/* Display notification if the audio fails to load.
		   Attach this to both audio and source for best compatibility. */
		$("#audio, #audiosrc").on("error", function () {
			$("#audioerror").css("display", "block");
		});
	});



function styleTypeUpdate() {
	var checkFound = false;
	if ($('#textstyle').is(':checked')) {
		$('#textinfo').show();
		checkFound = true;
	}
	else 
		$('#textinfo').hide();

	if ($('#imagestyle').is(':checked')) {
		$('#imageinfo').show();
		checkFound = true;
	} 
	else 
		$('#imageinfo').hide();

	if ($('#svgstyle').is(':checked')) {
		$('#svginfo').show();
		checkFound = true;
	} 
	else {
		$('#svginfo').hide();
	}
	if (!checkFound) {
		// Set a default
		$('#textinfo').show();
		$('#textstyle').prop('checked', true);
		
	}
}

/* Add a line for SVG param/value TODO make this work */
function addsvginput (buttn) {
	/* The argument is the button whose parent needs to be cloned */
	var litem = $(buttn).parent();
	var newitem = litem.clone();
	newitem.find("input").val("");
	litem.after(newitem);
}

/* Remove a text input for SVG param */
function removesvginput (buttn) {
	var litem = $(buttn).parent();
	var list = litem.parent();
	// Don't delete last item!
	if (list.find(".svgparamitem").length > 1)
		litem.remove();
	
}

/* Set the value of the dochain field when the submit and continue button
   is clicked */
function chainOn () {
	$('#dochain').attr("value", "yes");
}

/* Clear the value of the dochain field when the plain submit button
   is clicked */
function chainOff () {
	$('#dochain').attr("value", "");
}
</script>


</body>
</html>
