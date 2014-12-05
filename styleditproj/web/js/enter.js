

var styleTemplateDiv;
var textInfoDiv;
var svgInfoDiv;
var imageInfoDiv;
var logoInfoDiv;
var blockInfoDiv;
var mainForm;
var divSuffix = 0;

window.onerror = function(msg, url, line, col, error) {
   // Note that col & error are new to the HTML 5 spec and may not be 
   // supported in every browser.  It worked for me in Chrome.
   var extra = !col ? '' : '\ncolumn: ' + col;
   extra += !error ? '' : '\nerror: ' + error;

   // You can view the information in an alert to see things working like this:
   alert("Error: " + msg + "\nurl: " + url + "\nline: " + line + extra);

   // TODO: Report this error via ajax so you can keep track
   //       of what pages have JS issues

   var suppressErrorAlert = true;
   // If you return true, then error alerts (like in older versions of 
   // Internet Explorer) will be suppressed.
   return suppressErrorAlert;
};

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
	addFirstStyle();
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
	else if (buttn.hasClass('svgstyle'))
		newsrc = svgInfoDiv;
	else if (buttn.hasClass('imagestyle'))
		newsrc = imageInfoDiv;
	else if (buttn.hasClass('logostyle'))
		newsrc = logoInfoDiv;
	else if (buttn.hasClass('blockstyle'))
		newsrc = blockInfoDiv;
	var segment = buttn.closest('.styletemplate');
	var varinfo = segment.find('.varinfo');
	varinfo.empty();
	varinfo.append(newsrc.clone());
	console.log("newsrc:");
	console.log(newsrc.html());
	console.log("segment:");
	console.log(segment.html());
}

/* Add the first style */
function addFirstStyle () {
	var newInstance = styleTemplateDiv.clone();
	$('#mainform').append(newInstance);
	var checkedButton = newInstance.find('.textstyle');
	checkedButton.attr('checked', true);
	styleTypeUpdate(checkedButton);
	makeNamesUnique(newInstance);
}

/* Add a style after the one whose add button was just clicked */
function addStyle (buttn) {
	var curInstance = buttn.closest('.styletemplate');
	var newInstance = styleTemplateDiv.clone();
	curInstance.after (newInstance);
	console.log("newInstance:");
	console.log (newInstance.html);
	var checkedButton = newInstance.find('.textstyle');
	checkedButton.attr('checked', true);
	styleTypeUpdate(checkedButton);
	makeNamesUnique(newInstance);
}

/* Remove a style containing the Remove button that was just clicked. */
function removeStyle (buttn) {
	// TBI
	var curInstance = buttn.closest('.styletemplate');
	var list = curInstance.parent();
	if (list.find('.styletemplate').length > 1)
		curInstance.remove();
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

/* Fix up the input names in a division to be different from
   all other divisions. The divs are all built while the page
   is in active, so we can use a var to track the next suffix
   to use. */
function makeNamesUnique (div) {
	var suffixStr = divSuffix.toString();
	divSuffix++;
//	$("input[name='styletype']").attr("name", "styletype" + suffixStr);
//	$("input[name='textcontent']").attr("name", "textcontent" + suffixStr);
	
	//bleah. I can select all input elements that have the attribute name,
	//pull in the attribute, and replace it.
	var inputs = div.find("input[name],select[name]");
	inputs.each (function (idx) {
		var nam = $(this).attr("name");
		$(this).attr("name", nam + suffixStr);
	});
	// Should I also fix IDs and label "for" attributes?
	inputs = div.find("input[id]");
	inputs.each (function (idx) {
		var id = $(this).attr("id");
		$(this).attr("id", id + suffixStr);
	});
}
