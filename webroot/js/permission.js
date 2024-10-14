/**
 * $Id: permission.js,v 1.1 2022/07/11 02:02:42 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:42 $
 *  
 * @copyright Copyright 2007, Fonsen Technology Ltd. Corp.
 */
/**
 * Javascript library of permission interaction
 */
function checkCategoryBox(obj, id) {
	if(obj.checked == true) {
		$$("input[id="+id+"]").each(function(s) {s.checked=true;});
	}else{
		$$("input[id="+id+"]").each(function(s) {s.checked=false;});
	}
}

function checkSectionBox(obj, name) {
	if(obj.checked == true) {
		$$("input[title="+name+"]").each(function(s) {s.checked=true;});
	}else{
		$$("input[title="+name+"]").each(function(s) {s.checked=false;});
	}
}