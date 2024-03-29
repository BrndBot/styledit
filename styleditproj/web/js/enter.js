/*	enter.js
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2015
*/

var styleTemplateDiv;
var textInfoDiv;
var svgInfoDiv;
var imageInfoDiv;
var logoInfoDiv;
var blockInfoDiv;
var mainForm;
var selectedCategory;
var selectedModel;

/* An aid to debugging. Remove in the final version. */
window.onerror = function(msg, url, line, col, error) {
   // Note that col & error are new to the HTML 5 spec and may not be 
   // supported in every browser.  It worked for me in Chrome.
   var extra = !col ? '' : '\ncolumn: ' + col;
   extra += !error ? '' : '\nerror: ' + error;

   // You can view the information in an alert to see things working like this:
   alert("Error: " + msg + "\nurl: " + url + "\nline: " + line + extra);

   var suppressErrorAlert = true;
   // If you return true, then error alerts (like in older versions of 
   // Internet Explorer) will be suppressed.
   return suppressErrorAlert;
};

/* This is done when the page is loaded. (Shortcut for document ready) */
$(function () {
	// Set variables for the style bank blocks, so we
	// only have to point at them once.
	styleTemplateDiv = $('#formbank .styletemplate');
	textInfoDiv = $('#formbank .textinfo');
	svgInfoDiv = $('#formbank .svginfo');
	imageInfoDiv = $('#formbank .imageinfo');
	logoInfoDiv = $('#formbank .logoinfo');
	blockInfoDiv = $('#formbank .blockinfo');
	mainForm = $('#mainform');
	selectedCategory = $('#selectedcategory').text();
	selectedModel = $('#selectedmodel').text();
	addFirstStyle();
	setSelectedOrg();
	updateOrgBasedSels();
	populateByModel();
	catSelectUpdate();
	modelSelectUpdate();
});

/* Not used. Save for possible use later. */
function cloneTemplate(sel) {
	var contents = sel.html();
	var copy = $('<div></div>');
	$('body').append(copy.append(contents));
	return copy;
}

/* styleTypeUpdate rips out the old contents to replace them with a copy of
   the appropriate div. The first part of a style's form fields is
   constant, so we replace only the variable part.
*/
function styleTypeUpdate(buttn) {
	var newsrc = null;
	var hdrtxt = null;
	var hideFieldName = true;
	if (buttn.hasClass('textstyle')) {
		newsrc = textInfoDiv;
		hideFieldName = false;
		hdrtxt = "Text";
	}
	else if (buttn.hasClass('svgstyle')) {
		newsrc = svgInfoDiv;
		hdrtxt = "SVG";
	}
	else if (buttn.hasClass('imagestyle')) {
		newsrc = imageInfoDiv;
		hideFieldName = false;
		hdrtxt = "Image";
	}
	else if (buttn.hasClass('logostyle')) {
		newsrc = logoInfoDiv;
		hdrtxt = "Logo";
	}
	else if (buttn.hasClass('blockstyle')) {
		newsrc = blockInfoDiv;
		hdrtxt = "Block";
	}
	var segment = buttn.closest('.styletemplate');
	segment.find('.typehdr').text(hdrtxt);
	
	var varinfo = segment.find('.varinfo');
	varinfo.empty();
	varinfo.append(newsrc.clone());
	fieldNameBox = segment.find('.fieldnamebox');
	if (hideFieldName) {
		// Field names apply only to text and image
		fieldNameBox.hide();
	} else {
		fieldNameBox.show();
	}
	makeNamesUnique();
}

/* Add the first style */
function addFirstStyle () {
	var newInstance = styleTemplateDiv.clone();
	$('#globalfields').after (newInstance);
	var checkedButton = newInstance.find('.textstyle');
	checkedButton.attr('checked', true);
	styleTypeUpdate(checkedButton);
}

/* Add a style after the one whose add button was just clicked */
function addStyle (buttn) {
	var curInstance = buttn.closest('.styletemplate');
	var newInstance = styleTemplateDiv.clone();
	curInstance.after (newInstance);
	var checkedButton = newInstance.find('.textstyle');
	checkedButton.attr('checked', true);
	styleTypeUpdate(checkedButton);
}

/* Remove a style containing the Remove button that was just clicked. */
function removeStyle (buttn) {
	// TBI
	var curInstance = buttn.closest('.styletemplate');
	var list = curInstance.parent();
	if (list.find('.styletemplate').length > 1)
		curInstance.remove();
}

/* Show or hide the custom color based on the palette selection.
 * It assumes that sel is contained in a li element and that the 
 * next li element contains the custom color selector. */
function showHideCustom (sel) {
	option = sel.val();
	picker = sel.parent().next();
	if (option == "palettecustom") {
		picker.show();
	} else {
		picker.hide();
	}
}

/* Add a line for SVG param/value 
   The argument is the button whose parent needs to be cloned */
function addSVGInput (buttn) {
	var litem = $(buttn).parent();
	var newitem = litem.clone();
	newitem.find("input").val("");
	litem.after(newitem);
}

/* Remove a text input for SVG param */
function removeSVGInput (buttn) {
	var litem = $(buttn).parent();
	var list = litem.parent();
	// Don't delete last item!
	if (list.find(".svgparamitem").length > 1)
		litem.remove();
	
}

/* Fix up the input names in a division to be different from
   all other divisions. Really want to make them sequential
   too, so we can create XML in the proper order. */
function makeNamesUnique () {
	$('div.styletemplate').each (function (idx0) {
		//Select all input elements that have the attribute "name",
		//pull in the attribute, and change the value to end with -idx0.
		//If there's already a suffix, replace it.
		//If the parameter name ends in [], keep the brackets at the end.
		var inputs = $(this).find("input[name],select[name],textarea[name]");
		inputs.each (function (idx) {
			var nam = $(this).attr("name");
			$(this).attr("name", suffixName(nam, idx0));
		});
		// Also fix IDs
		inputs = $(this).find("input[id]");
		inputs.each (function (idx) {
			var id = $(this).attr("id");
			$(this).attr("id", suffixName(id, idx0));
		});
	});
}

/* This function takes a string and appends "-n" where n is the argument.
   If there's already a -n suffix, it first strips off the old one. */
function suffixName (nam, n) {
	var isArray = nam.indexOf("[]") > 0;
	if (isArray) {
		// chop of the brackets first
		nam = nam.substr(0, nam.length - 2);
	}
	var hyphenIdx = nam.indexOf("-");
	if (hyphenIdx > 0)
		nam = nam.substr(0, hyphenIdx); 
	var val = nam + '-' + n;
	// Now put the brackets back if they were removed
	if (isArray) {
		val = val + '[]';
	}
	return val;
}

/* Use the hidden "selectedorg" div to set the initial value for the
 * organization. orgname is the ID of the select element.
 */
function setSelectedOrg() {
	savedorg = $('#selectedorg');
	if (savedorg.length > 0) {
		console.log ("saved org " + savedorg.text());
		selectIfExists($('#orgname'), savedorg.text());
		selectIfExists($('#morgname'), savedorg.text());
	}
}

/* Set the chosen option if available.
 * sel is a JQuery selector for a select element.
 * option is the string for the option to set. */
function selectIfExists(sel, option) {
	console.log ("Selecting " + option);
	sel.children().each(function() {
		if ($(this).text() == option) {
			sel.val(option).change();
			console.log ("Matching option found, selection is now " + sel.val());
			return;
		}
	});
}

/* Update the "brand personalities" and "models" SELECT
   elements to match the SELECTed organization. If there are
   saved values, apply those if possible (which it may not
   be if the organization has changed). */
function updateOrgBasedSels() {
	var org = $('#orgname').val();
	if (!org)
		return;
	var brandid = '#brand-' + org.replace(/\s/g,'');
	var branddiv = $(brandid);

	$('#brand').empty();
	$('#brand').append(branddiv.find("option").clone());
	
	// Now same for models. Need to get them under all categories,
	// so we look at all children of #orgmodels whose ID starts with
	// 'model-Orgname'.
//	var modeldivs = $('#orgmodels > div');
//	var ourdivs = $([]);
//	var modelPrefix = 'model-' + org;
//	$('#model').empty();
//	modeldivs.each (function () {
		// Retain only matching IDs
//		var modeldiv = $(this);
//		var modeldivid = modeldiv.attr("id");
//		if (modeldivid.indexOf(modelPrefix) == 0) {
//			$('#model').append (modeldiv.find("option").clone());
//		}
//	});
	
	// Now set to the saved values, if available
	savedbrand = $('#selectedbrand');
	if (savedbrand.length > 0) {
		selectIfExists($('#brand'), savedbrand.text());
	}
	savedcategory = $('#selectedcategory');
	if (savedcategory.length > 0) {
		selectIfExists($('#promo'), savedcategory.text());
	}
}


/*******************************************************************
 * Functions for model-driven actions ******************************
 */

/* If the div #modellayout isn't empty, use its spans as
 * directions to create the appropriate number and type of
 * style panels.
 */
function populateByModel() {
	// The modellayout div contains one div for each field, and it
	// contains two spans, with the style and field names respectively.
	var fields = $('#modellayout').find("div");
	var fieldStyles = [];
	var fieldNames = [];
	var nFields = fields.length;
	if (nFields == 0)
		return;
	
	fields.each(function () {
		fieldStyles.push($(this).find(".stylename").text());
		fieldNames.push($(this).find(".fieldname").text());
	});
	
	// Initially there is one style div, but for generality count them
	// and bring the number up to the number of fields.
	var existingStyles = $("#mainform .styletemplate");
	var lastStyle = existingStyles.last();
	for (i = 0; i < nFields - existingStyles.length; i++) {
		var newInstance = styleTemplateDiv.clone();
		lastStyle.after (newInstance);
		lastStyle = newInstance;
	}
	$("#mainform .styletemplate").each(function (n) {
		if (n < fieldStyles.length) {
			console.log ("Field name " + fieldNames[n]);
			console.log ("Field style " + fieldStyles[n]);
			var checkedButton = $(this).find('.' + fieldStyles[n] + 'style');
			checkedButton.attr('checked', true);
			var titleText = $(this).find('.fieldname1');
			titleText.text(fieldNames[n]);
			titleText.show();
			
			// Fill in the field name
			var fieldTextBox = $(this).find('.fieldnamebox input');
			fieldTextBox.val(fieldNames[n]);
			$(this).find(".stylesel").hide();	// Style types are fixed, so hide the buttons
			styleTypeUpdate(checkedButton);
		}
	});
}

/*******************************************************************
 * Functions for the model selection form **************************
 */

/* In the model selection form, populate the categories pulldown menu
   based on the selected organization. */
function catSelectUpdate () {
	// Get the set of options which is appropriate for the organization
	// from a hidden div.
	var orgid = 'cat-' + $('#morgname').val();
	console.log ("catSelectUpdate orgid = " + orgid);
	//var catid = 'cat-' + $('#mcategory').val();
	var catdiv = $('#orgcategories').find('#' + orgid);
	$('#mcategory').empty();
	$('#mcategory').append(catdiv.find("option").clone());
	
	// If there's a selected category, select it.
	selectIfExists ($('#mcategory'), selectedCategory);
	
	modelSelectUpdate();
}


/* In the model selection form, populate the models pulldown menu
 * based on the selected organization and category. */
function modelSelectUpdate() {
	console.log ("modelSelectUpdate");
	var orgcatid = 'model-' + $('#morgname').val() + '-' + $('#mcategory').val();
	// Turn spaces into underscores, so we have a legitimate HTML attribute
	orgcatid = orgcatid.replace (/ /g, "_");
	var modeldiv = $('#orgmodels').find("#" + orgcatid);
	$('#mmodel').empty();
	$('#mmodel').append(modeldiv.find("option").clone());
	// See if there is a selected model
//	if ($('#modelName').length > 0) {
//		selectIfExists ($('#model'), $('#modelName').val());
//	}
}
