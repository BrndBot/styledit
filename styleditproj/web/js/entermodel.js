/*	entermodel.js
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
*/

$(document).ready(function () {
	setSelectedOrg();
	catSelectUpdate();
});

/* Use the hidden "selectedorg" div to set the initial value for the
 * organization. orgname is the ID of the select element.
 */
function setSelectedOrg() {
	savedorg = $('#selectedorg');
	if (savedorg.length > 0) {
		selectIfExists($('#orgname'), savedorg.text());
	}
}

function selectIfExists(sel, option) {
	sel.children().each(function() {
		if ($(this).text() == option) {
			sel.val(option);
			return;
		}
	});
}

/* Add a line for a style type 
   The argument is the button whose parent needs to be cloned */
function addStyleType (buttn) {
	var trow = $(buttn).closest(".styletypetr");
	var newitem = trow.clone();
	newitem.find(".textbox").val("");
	trow.after(newitem);
}

/* Remove a line for a style type */
function removeStyleType (buttn) {
	var trow = $(buttn).closest(".styletypetr");
	var table = trow.parent();
	// Don't delete last item!
	if (table.find(".styletypetr").length > 1)
		trow.remove();
	
}

/* In the model selection form, populate the categories pulldown menu
based on the selected organization. */
function catSelectUpdate () {
	// Get the set of options which is appropriate for the organization
	// from a hidden div.
	var orgid = 'cat-' + $('#orgname').val();
	console.log ("catSelectUpdate orgid = " + orgid);
	var catdiv = $('#orgcategories').find('#' + orgid);
	$('#category').empty();
	$('#category').append(catdiv.find("option").clone());
	
	modelSelectUpdate();
}

/* In the model selection form, populate the models pulldown menu
* based on the selected organization and category. */
function modelSelectUpdate() {
	console.log ("modelSelectUpdate");
	var orgcatid = 'model-' + $('#orgname').val() + '-' + $('#category').val();
	// Turn spaces into underscores, so we have a legitimate HTML attribute
	orgcatid = orgcatid.replace (" ", "_");
	var modeldiv = $('#orgmodels').find("#" + orgcatid);
	$('#model').empty();
	$('#model').append(modeldiv.find("option").clone());
	// See if there is a selected model
	if ($('#modelName').length > 0) {
		selectIfExists ($('#model'), $('#modelName').val());
	}
}
