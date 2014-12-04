/* enter.js
 *
 *  CONFIDENTIAL
 *  
 *  All rights reserved by Brndbot, Ltd. 2014
 */


var styleTemplateDiv;
var textInfoDiv;
var svgInfoDiv;
var imageInfoDiv;
var logoInfoDiv;
var blockInfoDiv;
var mainForm;

$(document).ready(
	function () {
		// Set variables for the style bank blocks, so we
		// only have to point at them once.
		styleTemplateDiv = $('#formbank.styletemplate');
		textInfoDiv = $('#formbank.textinfo');
		svgInfoDiv = $('#formbank.svginfo');
		imageInfoDiv = $('#formbank.imageinfo');
		logoInfoDiv = $('#formbank.logoinfo');
		blockInfoDiv = $('#formbank.blockinfo');
		mainForm = $('#mainform');
	});


/* The new styleTypeUpdate needs to know which style we're changing,
   and it rips out the old contents to replace them with a copy of
   the appropriate div. The first part of a style's form fields is
   constant, so we replace only the variable part.
*/
function styleTypeUpdate(buttn) {
	var newsrc = null;
	if (buttn.hasClass('textstyle'))
		newsrc = textInfoDiv;
	else if (buttn.hasClass(svgstyle'))
		newsrc = svgInfoDiv;
	else if (buttn.hasClass('imagestyle'))
		newsrc = imageInfoDiv;
	else if (buttn.hasClass('logostyle'))
		newsrc = logoInfoDiv;
	else if (buttn.hasClass('blockstyle'))
		newsrc = blockInfoDiv;
	segment.html(newsrc.html());
}

/* Add a style after the one whose add button was just clicked */
function addStyle (buttn) {
	var curInstance = buttn.closest('.styletemplate');
	newInstance = curInstance.after ('<div class="styletemplate"></div>');
	// Does after actually return the new instance as a jquery object? Test.
	newInstance.html(styleTemplateDiv.html());
	styleTypeUpdate(newInstance, newInstance('.textstyle'));
}

/* Remove a style containing the Remove button that was just clicked. */
function removeStyle (button) {
	// TBI
	var curInstance = buttn.closest('.styletemplate');
	
}

/* Add a line for SVG param/value  */
/* The argument is the button whose parent needs to be cloned */
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

</script>

