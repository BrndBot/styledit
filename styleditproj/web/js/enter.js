

var styleTemplateDiv;
var textInfoDiv;
var svgInfoDiv;
var imageInfoDiv;
var logoInfoDiv;
var blockInfoDiv;
var mainForm;

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


/* styleTypeUpdate rips out the old contents to replace them with a copy of
   the appropriate div. The first part of a style's form fields is
   constant, so we replace only the variable part.
*/
function styleTypeUpdate(buttn) {
	var newsrc = null;
	var hdrtxt = null;
	if (buttn.hasClass('textstyle')) {
		newsrc = textInfoDiv;
		hdrtxt = "Text";
	}
	else if (buttn.hasClass('svgstyle')) {
		newsrc = svgInfoDiv;
		hdrtxt = "SVG";
	}
	else if (buttn.hasClass('imagestyle')) {
		newsrc = imageInfoDiv;
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
	makeNamesUnique();
}

/* Add the first style */
function addFirstStyle () {
	var newInstance = styleTemplateDiv.clone();
//	$('#mainform').append(newInstance);
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
	console.log("newInstance:");
	console.log (newInstance.html);
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
   all other divisions. Really want to make them sequential
   too, so we can create XML in the proper order. */
function makeNamesUnique () {
	$('div.styletemplate').each (function (idx0) {
		//Select all input elements that have the attribute "name",
		//pull in the attribute, and change the value to end with -idx0.
		//If there's already a suffix, replace it.
		var inputs = $(this).find("input[name],select[name]");
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
	var hyphenIdx = nam.indexOf("-");
	if (hyphenIdx > 0)
		nam = nam.substr(0, hyphenIdx) 
	return nam + '-' + n;
}
