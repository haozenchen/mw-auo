/**
 * $Id: div.js,v 1.1 2022/07/11 02:02:41 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:41 $
 * @copyright Copyright 2007, Fonsen Technology Ltd. Corp.
 */
/**
 * Javascript library of div element
 */      
function showDiv(divId){
	document.getElementById(divId).style.visibility = "visible";     
} 
function hideDiv(divId){
	document.getElementById(divId).style.visibility = "hidden";
}
function clearDiv(divId){
	document.getElementById(divId).innerHTML = "";
}
function setDivContent(divId, Content) {
	document.getElementById(divId).innerHTML = Content;
}
function showCommafy(number) {
	number = number + "";
	var pos = /(-?\d+)(\d{3})/;
	while (pos.test(number)) {
		number = number.replace(pos, "$1,$2");
	}
	return number;
}

