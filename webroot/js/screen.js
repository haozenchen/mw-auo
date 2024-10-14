/**
 * $Id: screen.js,v 1.1 2022/07/11 02:02:42 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:42 $
 *  
 * @copyright Copyright 2007, Fonsen Technology Ltd. Corp.
 */
/**
 * Javascript library of screen interaction
 */
var scrCbxUse = 'N';
var allCheckStat = false;
var msgDiaglogAddName = "_" + "msg";
var allowEmpty = true;

function emmaDialog(dialogId, msg) {
  var msgDialogId; 
  msgDialogId = dialogId + msgDiaglogAddName;
  Element.update(msgDialogId, msg);
  Element.show(dialogId);
}

function holdCbx() {
  scrCbxUse = 'Y';
}

function unholdCbx() {
  scrCbxUse = 'N';
}

function cbxHeld() {
  if (scrCbxUse == 'Y') {
    return true;
  } else {
    return false;
  }
}

/**
 * check all checkbox, or uncheck all
 * we can supply arguments (or not), first param to give search string
 */
function checkAll() {
	search = 'input[type=checkbox]';
	if (arguments[0]) {
		search = arguments[0];
	}
	$elems = $$(search);

	$elems.each(function(s) { 
			if (allCheckStat == true) {
				s.checked = false;
			} else {
				s.checked = true ;
			}
	});
	allCheckStat = !allCheckStat;
}

/**
 * check if all checkbox (with search pattern or not) is checked
 * if 2nd parameter (flag box id) is given, we will check it by the way
 */
function checkIfAllChecked() {
	search = 'input[type=checkbox]';
	flagBoxId = false;
	if (arguments[0]) {
		search = arguments[0];
	}
	if (arguments[1]) {
		flagBoxId = arguments[1];
	}
	$elems = $$(search);
	allChecked = true;
	$elems.each(function(s) { 
			if (s.checked == false) {
				allChecked = false;
			}
	});
	if (flagBoxId) {
		// we help check the flag box
		$(flagBoxId).checked = allChecked;
		allCheckStat = allChecked;
	}
	return allChecked;
}

/**
 * block parent element's event
 */
function blockParentEvent(event) {
	if (!event) {
		event = window.event;	// IE
	}
	if (event.stopPropagation) {
		event.stopPropagation();	// standard, but IE6 not support
	} else {
		event.cancelBubble = true;
	}
}

/**
 * include javascript lib on demand, even in ajax
 */
function includeJsOnDemand(jsPath, jsId, reload) {
	if (typeof reload == 'undefined') {
		reload = false;
	}
	if (! reload && document.getElementById(jsId)) {
		// already loaded
		return;
	}
	var head = document.getElementsByTagName('head')[0];
	//delay js load time
	/*for(i = 0; i < 100000; i++) {
	}*/
	var script = document.createElement('script');
	script.id = jsId;
	script.type = 'text/javascript';
	script.src = jsPath + '.js';
	head.appendChild(script);
};

/**
 * center the element in screen
 * @param object element to center
 */
function centerElement(elem) {
	var ua = navigator.userAgent.toLowerCase();
	var dh = parseInt(elem.offsetHeight);
	var dw = parseInt(elem.offsetWidth);
	if (ua.indexOf('msie') != -1) {
		var wh = document.documentElement.clientHeight;
		var ww = document.documentElement.clientWidth;
		var ofs = document.viewport.getScrollOffsets();
		elem.style.top = (wh/2 + ofs.top - dh/2);
		elem.style.left = (ww/2 - dw);
	} else {
		elem.style.marginTop = '-' + dh/2 + 'px';
		elem.style.marginLeft = '-' + dw/2 + 'px';
	}
}

/**
 * update select element options
 * @param string id Select element ID
 * @param int index Index in cglistOpts
 * @param object cglistOpts json formatted (use cake javascript::object()) object from cakephp find('list') result with grouping
 */
function updateSelectOpts(id, index, cglistOpts, allowEmpty) {
	var select = $(id);
	var l1 = cglistOpts[index];	// get into level 1 of list
	var n = 0;
	select.options.length = 0;	// clear options
	if (allowEmpty === true) {
		select.options[0] = new Option('', '');	// allow empty
		n++;
	}
	for (var i in l1) {
		// get into level 2
		select.options[n] = new Option(l1[i], i);
		n++;
	}
	return true;
}

function addSelectOpts(id, opts) {
	var select = $(id);
	var n = select.options.length;
	for (var i in opts) {
		select.options[n] = new Option(opts[i], i);
		n++;
	}
}

function addSelectRocYear() {
	if (arguments[0]) {
		cssRule = arguments[0];
	} else {
		cssRule = 'id$=Year';	// default
	}
	if (arguments[1]) {
		eventName = arguments[1];
	} else {
		eventName = 'change';	// default
	}
	$$('select[' + cssRule + ']').each(function(elem) {
		Event.observe(elem, eventName, function(evt) {
			var evtElem = Event.findElement(evt, 'SELECT');
			var len = evtElem.options.length;
			for (var i = 0; i < len; i++) {
				 txt = evtElem.options[i].text;
				 if(txt + 'x' == 'x') {
				 	continue;
				 }
				 txt = parseInt(txt);
				 txt = txt + '(' + (txt - 1911) + ')';
				 evtElem.options[i].text = txt;
			}
		})
	});
}
