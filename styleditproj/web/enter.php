<?php
/*	enter.php
	
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
*/

require_once ('bin/fontfile.php');
require_once ('bin/orgdir.php');
require_once ('bin/modelfile.php');
require_once('bin/loggersetup.php');

header("Content-type: text/html; charset=utf-8");

session_start();
include('bin/sessioncheck.php');
if (!sessioncheck())
	return;
$logger->info("enter.php");
?>


<html lang="en">
<head>
	<title>Enter Styleset</title>
	<link href="css/styles.css" rel="stylesheet">
	
</head>
<body>
<noscript><strong>Sorry, JavaScript is required.</strong>
</noscript>

<?php

/* Get URL parameters */	
$modelparm = null;
if (array_key_exists("model", $_GET))
	$modelparm = $_GET["model"];
$categoryparm = null;
if (array_key_exists("category", $_GET))
	$categoryparm = $_GET["category"];
$orgparm = null;
if (array_key_exists("org", $_GET))
	$orgparm = $_GET["org"];

$modelFile = null;


/* See if the URL specifies a model file */
if ($modelparm && $categoryparm && $orgparm) {
	$modelFile = ModelFile::findModel($modelparm, $categoryparm, $orgparm);
	// create a series of spans, each containing the name of a style type.
	// Store the field names and style types.
	if ($modelFile)
		$modelFile->loadModelInfo($orgparm);
}

?>
<ul class="nobullet">
<li><a href="entermodel.php">Switch to model editor</a>
</ul>

<?php
/* The form for picking a model and reloading the form */
?>
<form action="enter.php" accept-charset="UTF-8">
<h1>Set up form for model</h1>
<table>
<tr><td>Organization:</td>
<td>
	<select name="org" id="morgname" onchange="catSelectUpdate();" >
<?php
/* Populate the organizations pulldown in the model selection form */
function fillOrgOptions ($canLimit) {
	global $modelFile;
	global $logger;
	if ($canLimit && isset ($modelFile)) {
		// Fill in only the organization for the selected model
		echo ("<option>" . $modelFile->organization . "</option>\n");
	} else {
		// Fill in the organizations pulldown menu 
		$logger->info ("Adding organization options");
		reset (Organization::$organizations);
		foreach (Organization::$organizations as $org) {
			echo ("<option>" . $org->name . "</option>\n");
		}
	}
}

fillOrgOptions(false);
?>
	</select>
</td></tr>
<tr><td>
	Category:
</td><td>
	<select name="category" id="mcategory" onchange="modelSelectUpdate();">
	</select>
</td></tr>
<tr><td>
	Model:
</td><td>
	<select name="model" id="mmodel">
	</select>
</td></tr>
</table>
<div><input type="submit" class="submitbutton" value="Reload" >
</div>
</form>



<h1>Enter style information</h1>
<?php
if (isset ($modelFile)) {
	echo ("<div class='selectedmodelname'>Model: " . $modelFile->modelName . "</div>");	
}
?>
<div id="selectedmodel"></div>

<form id="mainform" 
		action="processform.php" 
		method="post" 
		accept-charset="UTF-8">
<?php
if (isset ($modelFile)) {
	echo ("<input type='hidden' id='modelName' value='" . $modelFile->modelName . "'>\n");
}
?>
<table id="globalfields">
<tr><td>Organization:</td>
<td>
	<select name="orgname" id="orgname" onchange="updateOrgBasedSels();">
<?php
	fillOrgOptions(true);
?>
	</select>
</td></tr>
<tr><td>Brand identity:</td>
<td>
	<select name="brand" id="brand">
	</select>
</td></tr>
<tr><td>Model:</td>
<td>
	<select name="model" id="model">
	</select>
</td></tr>
<tr><td>Channel:</td>
<td>
	<select multiple name="channel" id="channel">
		<option>Twitter</option>
		<option>Facebook</option>
	</select>
</td></tr>
<tr><td>
Style set name:</td> <td><input id="stylename" class="textbox" type="text" name="stylename" required>
</td></tr>
<tr><td>
Overall width: </td> <td><input id="promowidth" class="numberbox" type="number" min="1" name="promowidth" required>
</td></tr>
<tr><td>
Overall height: </td> <td><input id="promoheight" class="numberbox" type="number" min="1" name="promoheight" required>
</td></tr>
</table>
<?php
if ($modelparm) {
	echo ("<input type='hidden' name='model' value='${modelparm}'>\n");
}
?>

<ul class="nobullet">
<li >
<input type="submit" class="submitbutton" value="Submit" >
</li>
</ul>
</form>


<?php
/* The following div is a bank from which form elements can be copied as needed for each
   style that's added. It is always hidden.
   
   The divs are identified by class, since they may be replicated.
*/
?>
<div id="formbank" class="hidden">

<div class="styletemplate">
<p class="hidden fieldname1">
<ul class="nobullet stylesel" title="Select the type of style">
<li>
	<label>
		<input type="radio" class="textstyle" 
			name="styletype" value="text"
			onclick="styleTypeUpdate($(this));">
		Text
	</label></li>
<li>
	<label>
		<input type="radio" class="svgstyle" 
			name="styletype" value="svg"
			onclick="styleTypeUpdate($(this));">
		SVG
	</label></li>
<li>
	<label>
		<input type="radio" class="imagestyle" 
			name="styletype" value="image" 
			onclick="styleTypeUpdate($(this));">
		Image
	</label></li>
<li>
	<label>
		<input type="radio" class="blockstyle" 
			name="styletype" value="block" 
			onclick="styleTypeUpdate($(this));">
		Block
	</label>
<li>
	<label>
		<input type="radio" class="logostyle" 
			name="styletype" value="logo" 
			onclick="styleTypeUpdate($(this));">
		Logo
	</label>
<li>&nbsp;</li>
</ul> <!--  stylesel -->
<?php
/* The .typehdr div is the part of the style template that doesn't change.
 * IDs will be modified for each copy for the style template. */
?>
<div class="typehdr"></div>
<table>
<tr class="fieldnamebox"><td>
	Field name:
</td><td>
	<input type="text" name="fieldname">
</td></tr>
<tr><td>
Width: </td> <td><input id="stylewidth" class="numberbox" type="number" min="1" name="stylewidth" required>
</td></tr>
<tr><td>
Height: </td> <td><input id="styleheight" class="numberbox" type="number" min="1" name="styleheight" required>
</td></tr>
<tr>
<td>Anchor:</td>
<td>
<select name="anchor">
	<option value="tl">Top left</option>
	<option value="tr">Top right</option>
	<option value="bl">Bottom left</option>
	<option value="br">Bottom right</option>
</select>
</td><tr>
<tr><td>
Hor offset: </td> <td><input id="hoffset" class="numberbox" type="number" min="0" name="hoffset">
</td></tr>
<tr><td>
Vert offset: </td> <td><input id="voffset" class="numberbox" type="number" min="0" name="voffset">
</td></tr>
<tr>
</table>
<ul class="nobullet">
<li><label>
<input type="checkbox" name="hcenter" id="hcenter">
Center horizontally
</label>
</li>

</ul>

<?php
/* Content specific to the field type gets inserted in the .varinfo div. */
?>
<div class="varinfo"></div>

	<button type="button" onclick="addStyle($(this));">Add</button>
	<button type="button" onclick="removeStyle($(this));">Remove</button>
	<hr>
</div>	<!-- styletemplate -->


<div class="imageinfo">
<ul class="nobullet">
	<li class="imagepath">
		Image file path: <input class="numberbox" type="text" name="imagepath">
	<li>
		Opacity: <input class="numberbox" type="number" min="0" max="100" value="100" name="opacity">
	<li>
		<label >
			<input id="multiplycb" type="checkbox" name="multiply">
			Multiply
		</label>
		
</ul>
</div>		<!-- imageinfo -->


<div class="svginfo">

<textarea name="svg" rows="4" cols="60" placeholder="<svg>...</svg>"
	title="Paste SVG here, e.g., from Illustrator" >
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
<ul class="nobullet">
	<li>
	<span class="formlabel">Default content:</span>
	<input class="textbox" type="text" name="textcontent">
	<li>
	<span class="formlabel">Alignment:</span>
	<select name="alignment">
		<option  value="left" selected>Flush left</option>
		<option  value="right">Flush right</option>
		<option  value="center">Centered</option>
		<option  value="justified">Justified</option>
	</select>
	<li>
	<span class="formlabel">Font:</span>
	<select name="font">
<?php
	/* Fill in the font pulldown menu */
	reset (FontFile::$fonts);
	while (list($key, $val) = each(FontFile::$fonts)) {
		echo ("<option>$val</option>\n");
	}
?>
	</select>
	<li>
	<span class="formlabel">Point size:</span> <input class="numberbox" name="pointsize" type="number" min="0" max="300" required>
	<li>
	<label>
		<input type="checkbox" name="bold">
		<b>Bold</b>
	</label>
	<li>
	<label>
		<input type="checkbox" name="italic">
		<i>Italic</i>
	</label>
	<li>
	<label class="dropshadow">
		<input type="checkbox" name="dropshadow">
		Drop shadow
	</label>
	<li class="dropshadinfo">
		<table style="padding-left:32px;">
			<tr><td>H:</td><td> <input id="dropshadh" name="dropshadh" type="number" min="0"></td></tr>
			<tr><td>V:</td><td> <input id="dropshadv" name="dropshadv" type="number" min="0"></td></tr>
			<tr><td>Blur:</td><td> <input id="dropshadblur" name="dropshadblur" type="number" min="0"></td></tr>
		</table>
	</li>
	<li>
	<span class="formlabel">Color:</span>
	<select name="palette" onchange="showHideCustom($(this));">
		<option value="paletteone">Palette 1</option>
		<option value="palettetwo">Palette 2</option>
		<option value="palettethree">Palette 3</option>
		<option value="palettefour">Palette 4</option>
		<option value="palettecustom">Custom</option>
	</select>
	<li id="txtpalettecustom" style="display:none">
	Custom color:<input type="color" name="textcolor" value="#000000">
</ul>
</div>		<!-- textinfo -->

<div class="blockinfo">
<ul class="nobullet">
	<li>
	<span class="formlabel">Color:</span>
	<select name="palette" onchange="showHideCustom($(this));">
		<option value="paletteone">Palette 1</option>
		<option value="palettetwo">Palette 2</option>
		<option value="palettethree">Palette 3</option>
		<option value="palettefour">Palette 4</option>
		<option value="palettecustom">Custom</option>
	</select>
	<li id="blkpalettecustom" style="display:none">
	Custom color:<input type="color" name="blockcolor" value="#000000">
	<li>
	<label class="dropshadow">
		<input type="checkbox" name="dropshadow">
		Drop shadow
	</label>
	<li class="dropshadinfo">
		<table style="padding-left:32px;">
			<tr><td>H:</td><td> <input id="blockdropshadh" name="blockdropshadh" type="number" min="0"></td></tr>
			<tr><td>V:</td><td> <input id="blockdropshadv" name="blockdropshadv" type="number" min="0"></td></tr>
			<tr><td>Blur:</td><td> <input id="blockdropshadblur" name="blockdropshadblur" type="number" min="0"></td></tr>
		</table>
	</li>
	<li>
		Opacity: <input class="numberbox" type="number" min="0" max="100" value="100" name="opacity">
	<li>
		<label >
			<input id="multiplycb" type="checkbox" name="multiply">
			Multiply
		</label>
</ul>
</div>		<!-- blockinfo -->

<div class="logoinfo">
<ul class="nobullet">
	<li>
		<label class="dropshadow">
			<input id="logodropshadcb" type="checkbox" name="dropshadow">
			Drop shadow
		</label> 
	<li>
		<table style="padding-left:32px;">
			<tr><td>H:</td><td> <input id="logodropshadh" name="logodropshadh" type="number" min="0"></td></tr>
			<tr><td>V:</td><td> <input id="logodropshadv" name="logodropshadv" type="number" min="0"></td></tr>
			<tr><td>Blur:</td><td> <input id="logodropshadblur" name="logodropshadblur" type="number" min="0"></td></tr>
		</table>
</ul>
</div>		<!-- logoinfo -->

</div>	<!-- End formbank -->

<div id="brandbank" class="hidden">
<?php
	/* Build a set of divs which contain the brand identity menu options
	   for each organization */
	reset (Organization::$organizations);
	while (list($key, $org) = each(Organization::$organizations)) {
		$org->insertBrandIdentities();
	}
?>
</div>	<!-- End brandbank -->

<div id="promobank" class="hidden">
<?php
	/* Build a set of divs which contain the promotion (category) menu options
	   for each organization */
	reset (Organization::$organizations);
	foreach (Organization::$organizations as $org) {
		$org->insertCategories();
	}
?>
</div>	<!-- End promobank -->

<!--  Invisible div holding model layout -->
<div id="modellayout" class="hidden">
<?php 

//	$fields = $modelInfo[0];
//	$styles = $modelInfo[1];
if (isset ($modelFile)) {
	for ($i = 0; $i < sizeof($modelFile->fieldNames); $i++) {
		$style = $modelFile->styleTypes[$i];
		$field = $modelFile->fieldNames[$i];
		echo ("<div>");
		echo ("<span class='stylename'>" . $style . "</span>\n");
		echo ("<span class='fieldname'>" . $field . "</span>\n");
		echo ("</div>");
	}
}
?>
</div>

<!--  Invisible div holding categories for each organization -->
<div id="orgcategories" class="hidden">
<?php
	/* Build a set of divs which contain the model file names
	   for each organization and category */
	reset (Organization::$organizations);
	foreach (Organization::$organizations as $org) {
		$org->insertCategories();
	}
?>
</div>

<!--  Invisible div holding models for each category -->
<div id="orgmodels" class="hidden">
<?php
	/* Build a set of divs which contain the model file names
	   for each category */
	reset (Organization::$organizations);
	foreach (Organization::$organizations as $org) {
		foreach ($org->categories as $cat) {
			$org->insertModels($cat->name);
		}
	}
?>
</div>
<?php
	/* If the session variables 'org', 'brand' and 'category' are set, 
	   put them into divs with corresponding IDs */
	if (isset(Organization::$selectedOrg)) {
		echo("<div class=\"hidden\" id=\"selectedorg\">" .
			Organization::$selectedOrg .
			"</div>\n");
	} 
	if (isset(Organization::$selectedBrand)) {
		echo("<div class=\"hidden\" id=\"selectedbrand\">" .
			Organization::$selectedBrand .
			"</div>\n");
	} 
	if (isset(Organization::$selectedCategory)) {
		echo("<div class=\"hidden\" id=\"selectedpromo\">" .
			Organization::$selectedCategory .
			"</div>\n");
	} 
?>

<!-- Put scripts at end for faster load -->

<script type="text/JavaScript"
	src="http://code.jquery.com/jquery-1.11.1.js"></script>

<script type="text/javascript" src="js/enter.js"></script>


</body>
</html>
