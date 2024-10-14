/**
 * $Id: listdep.js,v 1.1 2022/07/11 02:02:42 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:42 $
 */
/**
 * List Department Lib
 * @copyright  Copyright 2007, Fonsen Technology Ltd. Corp.
 */
var tableindex = 0;
var allDeptIds = new Array();
var allDeptNames = new Array();
var allDeptLinks = new Array();
var deptTdPrfx = '';
var updDiv = '';

var d = new Date();
var t1 = d.getTime();
var timer;
var listTblRows = 15;

function tiClear(){
	clearTimeout(timer);
}

function initListDept(prfx, divName) {
  deptTdPrfx = prfx;
  upDiv = divName;
}

function setDept(deptIdx, deptId, deptName, deptLink) {
  allDeptNames[deptIdx] = deptName;
  allDeptLinks[deptIdx] = deptLink;
  allDeptIds[deptIdx] = deptId;
}

function TableIndex(step){
	
	tableindex = tableindex + step;
	if (tableindex < 0){
		tableindex = 0;
	}	else if (allDeptNames.length < (tableindex + listTblRows)){
		tableindex = allDeptNames.length - listTblRows;
	}
	ListDept(tableindex);
	timer = setTimeout("TableIndex(" + step + ");",200);
}

function ListDept(tindex){
	var objTd = new Array(listTblRows);
	var dispIndex;
	var tdHtml;

	for (i = 0; i < listTblRows; i++) {
		dispIndex = i + tindex;
		linkId = 'dplink_' + allDeptIds[dispIndex];
		tdHtml = '<a href="' + allDeptLinks[dispIndex] + '" id="' + linkId + '" onClick="return false;">' + allDeptNames[dispIndex] + '</a>';
		tdHtml += '<script>' + "Event.observe('" + linkId +"', 'click', function(event){ new Ajax.Updater('" + upDiv + "', '" + allDeptLinks[dispIndex] + "', {asynchronous:true, evalScripts:true, onLoading:function(request){Element.show('loading');}, onComplete:function(request, json){Element.hide('loading');}, requestHeaders:['X-Update', '" + upDiv + "']}) }, false);" + '</' + 'script>';
		if(allDeptIds[dispIndex]) {
			Element.update(deptTdPrfx + i, tdHtml);
		}
	}
}
