/*	entermodel.js
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
*/

$(function () {
	setSelectedOrg();
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
