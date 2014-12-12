<?php
/*	enter.php
	
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
*/

require_once ('bin/fontfile.php');
require_once ('bin/orgfile.php');

header("Content-type: text/html; charset=utf-8");

session_start();
include('bin/sessioncheck.php');
if (!sessioncheck())
	return;
error_log("enter.php");
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
	case "1":
		$errmsg = "Please specify a clip type.";
		break;	
	case "2":
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

<h1>Enter style information</h1>

<form id="mainform" 
		action="processform.php" 
		method="post" 
		accept-charset="UTF-8">
<table id="globalfields">
<tr><td>Organization:</td>
<td>
	<select name="orgname" id="orgname" onchange="updateOrgBasedSels();">
<?php
	/* Fill in the organizations pulldown menu */ 
	reset (Organization::$organizations);
	while (list($key, $org) = each(Organization::$organizations)) {
		echo ("<option>" . $org->name . "</option>\n");
	}
?>
	</select>
</td></tr>
<tr><td>Brand identity:</td>
<td>
	<select name="brand" id="brand">
	</select>
</td></tr>
<tr><td>Promotion:</td>
<td>
	<select name="promo" id="promo">
	</select>
</td></tr>
<tr><td>
Style set name:</td> <td><input id="stylename" class="textbox" type="text" name="stylename" required>
</td></tr>
<tr><td>
Overall width: </td> <td><input id="promowidth" class="textbox" type="number" min="1" name="promowidth" required>
</td></tr>
<tr><td>
Overall height: </td> <td><input id="promoheight" class="textbox" type="number" min="1" name="promoheight" required>
</td></tr>
</table>

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
<ul class="nobullet" title="Select the type of style">
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
<h4 class="typehdr"></h4>
<table>
<tr><td>
Width: </td> <td><input id="stylewidth" class="textbox" type="number" min="1" name="stylewidth" required>
</td></tr>
<tr><td>
Height: </td> <td><input id="styleheight" class="textbox" type="number" min="1" name="styleheight" required>
</td></tr>
<tr>
<td>Anchor:</td>
<td>
<select name="anchor">
	<option name="tl">Top left</option>
	<option name="tr">Top right</option>
	<option name="bl">Bottom left</option>
	<option name="br">Bottom right</option>
</select>
</td><tr>
</table>

</ul>

<div class="varinfo"></div>

	<button type="button" onclick="addStyle($(this));">Add</button>
	<button type="button" onclick="removeStyle($(this));">Remove</button>

<hr>
</div>	<!-- styletemplate -->


<div class="imageinfo">
<ul class="nobullet">
	<li class="imagepath">
		Image file path: <input class="textbox" type="text" name="imagepath">
	<li>
		Opacity: <input class="textbox" type="number" min="0" max="100" value="100" name="opacity">
	<li>
		<label >
			<input id="multiplycb" type="checkbox" name="multiply">
			Multiply
		</label>
		
</ul>
</div>		<!-- imageinfo -->


<div class="svginfo">

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
<ul class="nobullet">
	<li>
	Default content: <input class="textbox" type="text" name="textcontent">
	<li>
	Font:
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
	Point size: <input class="textbox" name="pointsize" type="number" min="0" max="300" required>
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
	Color:
	<select name="palette" onchange="showHideCustom($(this));">
		<option value="paletteone">Palette 1</option>
		<option value="palettetwo">Palette 2</option>
		<option value="palettethree">Palette 3</option>
		<option value="palettefour">Palette 4</option>
		<option value="palettecustom">Custom</option>
	</select>
	<li id="palettecustom" style="display:none">
	Custom color:<input type="color" name="textcolor" value="#000000">
</ul>
</div>		<!-- textinfo -->

<div class="blockinfo">
<ul class="nobullet">
	<li>
	Color:
	<select name="palette" onchange="showHideCustom($(this));">
		<option value="paletteone">Palette 1</option>
		<option value="palettetwo">Palette 2</option>
		<option value="palettethree">Palette 3</option>
		<option value="palettefour">Palette 4</option>
		<option value="palettecustom">Custom</option>
	</select>
	<li id="palettecustom" style="display:none">
	Custom color:<input type="color" name="blockcolor" value="#000000">
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
	/* Build a set of divs which contain the promotion menu options
	   for each organization */
	reset (Organization::$organizations);
	while (list($key, $org) = each(Organization::$organizations)) {
		$org->insertPromotions();
	}
?>
</div>	<!-- End promobank -->

<?php
	/* If the session variables 'org', 'brand' and 'promo' are set, 
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
	if (isset(Organization::$selectedPromo)) {
		echo("<div class=\"hidden\" id=\"selectedpromo\">" .
			Organization::$selectedPromo .
			"</div>\n");
	} 
?>

<!-- Put scripts at end for faster load -->

<script type="text/JavaScript"
	src="http://code.jquery.com/jquery-1.11.1.js"></script>

<script type="text/javascript" src="js/enter.js"></script>


</body>
</html>
