<?php
/*	enter.php
	
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
*/

require_once ('bin/orgdir.php');
require_once('bin/loggersetup.php');

header("Content-type: text/html; charset=utf-8");

session_start();
include('bin/sessioncheck.php');

if (!sessioncheck())
	return;
$logger->info("entermodel.php");
?>

<html lang="en">
<head>
	<title>Enter Model</title>
	<link href="css/styles.css" rel="stylesheet">
	
</head>
<body>
<noscript><strong>Sorry, JavaScript is required.</strong>
</noscript>

<ul class="nobullet">
<li><a href="enter.php">Enter styleset</a>
</ul>

<h1>Enter model information</h1>

<form id="mainform" 
		action="processmodelform.php" 
		method="post" 
		accept-charset="UTF-8">
<table>
<tr><td>Organization:</td>
<td>
	<select name="orgname" id="orgname" >
<?php
	/* Fill in the organizations pulldown menu */ 
	reset (Organization::$organizations);
	while (list($key, $org) = each(Organization::$organizations)) {
		echo ("<option>" . $org->name . "</option>\n");
	}
?>
	</select>
</td></tr>
<tr><td>
Model name:</td> <td><input id="modelname" class="textbox" type="text" name="modelname" required>
</td></tr>
<tr><td>
Description:</td> <td><input id="description" class="textbox" type="text" name="description" required>
</td></tr>
<tr><td>
Category:</td> <td><input id="category" class="textbox" type="text" name="category" required>
</td></tr>
</table>

<table>
<tr><th>Field name</th><th>Style type</th></tr>
<tr class="styletypetr"><td>
<input class="textbox" type="text" name="fieldname[]" required>
</td><td>
<select name="styletype[]">
	<option value="text">Text</option>
	<option value="image">Image</option>
	<option value="svg">SVG</option>
	<option value="block">Block</option>
	<option value="logo">Logo</option>
</select>
</td><td>
	<button type="button" onclick="addStyleType(this);">+</button>
</td><td>
	<button type="button" onclick="removeStyleType(this);">-</button>
</td></tr>

</table>

<ul class="nobullet">
<li >
<input type="submit" class="submitbutton" value="Submit" >
</li>
</ul>
</form>

<?php
	/* If the session variable 'org' is set, 
	   put it into div with corresponding ID */
	if (isset(Organization::$selectedOrg)) {
		echo("<div class=\"hidden\" id=\"selectedorg\">" .
			Organization::$selectedOrg .
			"</div>\n");
	} 
?>

<!-- Put scripts at end for faster load -->

<script type="text/JavaScript"
	src="http://code.jquery.com/jquery-1.11.1.js"></script>

<script type="text/javascript" src="js/entermodel.js"></script>


</body>
</html>
