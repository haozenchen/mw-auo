/**
 * $Id: hiddendiv.js,v 1.1 2022/07/11 02:02:41 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:41 $
 */   
/**
 * Javascript for Top Menu
 * @copyright   Copyright 2007, Fonsen Technology Ltd. Corp.
 */
function showPopMenu(menu_id) {
	$$('div[symbol=pop_menu]').each(
		function (el) { Element.hide(el);}
	);
	if(menu_id) {
		if($(menu_id)) {
			Element.show(menu_id);
		}
	}
}

function showSelDiv(obj) {
	if(obj == 1) {
		Element.hide(lectureDiv3);
		Element.show(lectureDiv1);
		Element.show(lectureDiv2);
	} else {
		Element.hide(lectureDiv1);
		Element.hide(lectureDiv2);
		Element.show(lectureDiv3);
	}
}

function tabClick(id, ttnum){
	var str = id.substring(0,id.length-1);
	for(i = 1; i <= ttnum; i++){
		document.getElementById(str+i).className='tabstyle2';
	}
	document.getElementById(id).className='tabstyle1';
}

function tabEboard(id, ttnum){
	for(i = 1; i <= ttnum; i++){
		document.getElementById('tab'+i).className='ebbutoff';
		if(i == ttnum) {
			document.getElementById('tab'+i).className='ebbutn';
		}
	}
	document.getElementById(id).className='ebbuton';
}

/**
 * all tabs in document
 */
var allTabs;

/**
 * add all tab
 */
function addAllTab(tabs) {
	allTabs = tabs;
}

/**
 * open specified tab
 */
function tabOpen(tabName) {
	for (var i=0; i<allTabs.length; i++) {
		Element.hide(allTabs[i]);
	}
	Element.show(tabName);
}

/**
 * this helps us gather referer in different browsers
 * we use this to avoid invalid intrusion from url
 */
function fsLink(url) {
	var browser = navigator.appName;
	if (browser == 'Microsoft Internet Explorer') {
		var e = document.createElement("a");
		e.href = url;
		document.body.appendChild(e);
		e.click();
	} else {
		location.href = url;
	}
	return true;
}

/**
 * state working of select menu
 */  

var baseMenuMouseState = '0';
var linkSelectMouseState = '0';

function showSelectMenu(menu_id, tmpId, buttonType) {
	$$('div[symbol=pop_menu]').each(
		function (el) { Element.hide(el);}
	);
	if(buttonType == 'base'){
		$$('span[symbol=baseBtnTouch]').each(
			function (el) {
				if(el){
					if(document.onmousedown){
						var objId = el.id;
						$(objId).style.backgroundColor = '#FFFFCC';
						$$('a[symbol=baseBtnA]').each(
							function (el) {
								$(el.id).style.color = '#555555';
							}
						);
						var imgObjId1 = 'touchImg1' + tmpId;
						var imgObjId2 = 'touchImg2' + tmpId;
						$(imgObjId1).show();
						$(imgObjId2).hide();
					}
				}
			}
		);
	}
	
	if(buttonType == 'link') {
		$$('span[symbol=linkBtnTouch]').each(
			function (el) {
				if(el){
					if(document.onmousedown){
						var objId = el.id;
						$(objId).style.backgroundColor = '#FFFFCC';
						$(objId).style.color = '#555555';
						var imgObjId1 = 'selectImg1' + tmpId;
						var imgObjId2 = 'selectImg2' + tmpId;
						$(imgObjId1).show();
						$(imgObjId2).hide();
					}
				}
			}
		);
	}
	
	if(menu_id) {
		if($(menu_id)) {
			Element.show(menu_id);
			if(buttonType == 'base') {
				var objId1 = 'baseMenu' + tmpId;
				$(objId1).style.backgroundColor = '#CC9900';
				var objAId = 'baseBtnA' + tmpId;
				$(objAId).style.color = '#FFFFFF';
				var objId2 = 'baseSelect' + tmpId;
				$(objId2).style.backgroundColor = '#CC9900';
				$(objId2).style.color = '#FFFFFF';
				var imgObjId1 = 'touchImg1' + tmpId;
				var imgObjId2 = 'touchImg2' + tmpId;
				$(imgObjId1).hide();
				$(imgObjId2).show();
				baseMenuMouseState = '1';
			}
			
			if(buttonType == 'link') {
				var objId3 = 'linkSelect' + tmpId;
				$(objId3).style.backgroundColor = '#CC9900';
				var imgObjId1 = 'selectImg1' + tmpId;
				var imgObjId2 = 'selectImg2' + tmpId;
				$(imgObjId1).hide();
				$(imgObjId2).show();
				linkSelectMouseState = '1';
			}
		}
	}
}

function menuOver(tmpId) {
	var cutStr = '';
	cutStr = tmpId.split('And');
	if(linkSelectMouseState == '0'){
		if(cutStr[0] == 'linkSelect') {
			var objId = cutStr[0] + cutStr[1];
			$(objId).style.backgroundColor = '#CC9900';
			var imgObjId1 = 'selectImg1' + cutStr[1];
			var imgObjId2 = 'selectImg2' + cutStr[1];
			$(imgObjId1).hide();
			$(imgObjId2).show();
		}
	}
	
	if(baseMenuMouseState == '0') {
		if(cutStr[0] == 'baseMenu') {
			var objId = cutStr[0] + cutStr[1];
			var objId2 = 'baseSelect' + cutStr[1];
			$(objId).style.backgroundColor = '#CC9900';
			var tmpAId = 'baseBtnA' + cutStr[1];
			$(tmpAId).style.color = '#FFFFFF';
			$(objId2).style.backgroundColor = '#CC9900';
			var imgObjId1 = 'touchImg1' + cutStr[1];
			var imgObjId2 = 'touchImg2' + cutStr[1];
			$(imgObjId1).hide();
			$(imgObjId2).show();
		}
	}
}

function menuOut(tmpId) {
	var cutStr = '';
	cutStr = tmpId.split('And');
	if(linkSelectMouseState == '0') {
		if(cutStr[0] == 'linkSelect') {
			var objId = cutStr[0] + cutStr[1];
			$(objId).style.backgroundColor = '#FFFFCC';
			var imgObjId1 = 'selectImg1' + cutStr[1];
			var imgObjId2 = 'selectImg2' + cutStr[1];
			$(imgObjId1).show();
			$(imgObjId2).hide();
		}
	}
	
	if(baseMenuMouseState == '0') {
		if(cutStr[0] == 'baseMenu') {
			var objId = cutStr[0] + cutStr[1];
			var objId2 = 'baseSelect' + cutStr[1];
			$(objId).style.backgroundColor = '#FFFFCC';
			var tmpAId = 'baseBtnA' + cutStr[1];
			$(tmpAId).style.color = '#555555';
			$(objId2).style.backgroundColor = '#FFFFCC';
			var imgObjId1 = 'touchImg1' + cutStr[1];
			var imgObjId2 = 'touchImg2' + cutStr[1];
			$(imgObjId1).show();
			$(imgObjId2).hide();
		}
	}
}

document.onmousedown = touchArea;

function touchArea(){
	
	//work in deleting user of right menu on schedule
	/*$$('table[id^=rightMenuAnd]').each(
		function (el) {
			if(el) {
				var tagId = el.id;
				$(tagId).style.visibility = 'hidden';
			}
		}
	);*/
	
	//work in deleting user of right menu on timeshifts
	/*$$('table[id^=rightTimeShiftsMenuAnd]').each(
		function (el) {
			if(el) {
				var tagId = el.id;
				$(tagId).style.visibility = 'hidden';
			}
		}
	);*/
	
	//work in select button
	if((baseMenuMouseState == '1')||(linkSelectMouseState == '1')) {
		$$('span[symbol=baseBtnTouch]').each(
			function (el) {
				if(el){
					if(document.onmousedown){
						var objId = el.id;
						$(objId).style.backgroundColor = '#FFFFCC';
						$(objId).style.color = '#555555';
						$$('a[symbol=baseBtnA]').each(
							function (el) {
									$(el.id).style.color = '#555555';
							}
						);
						$$('img[symbol=baseImg]').each(
							function (el) {
								if(el){
										var cutObjStr = el.id.substring(8,9);
								var cutObjId = el.id.substring(9);
										
									if(cutObjStr == '1') {
										var tmpObjId1 = 'touchImg' + cutObjStr + cutObjId;
										$(tmpObjId1).show();
									}else if(cutObjStr == '2') {
										var tmpObjId2 = 'touchImg' + cutObjStr + cutObjId;
											$(tmpObjId2).hide();
									}
								}
							}
						);
					}
					baseMenuMouseState = '0';
				}
			}
		);
	
		$$('span[symbol=linkBtnTouch]').each(
			function (el) {
				if(el){
					if(document.onmousedown){
						var objId = el.id;
						$(objId).style.backgroundColor = '#FFFFCC';
						$(objId).style.color = '#555555';
						$$('img[symbol=linkImg]').each(
							function (el) {
								if(el){
									var cutObjStr = el.id.substring(9,10);
									var cutObjId = el.id.substring(10);
								
									if(cutObjStr == '1') {
										var tmpObjId1 = 'selectImg' + cutObjStr + cutObjId;
										$(tmpObjId1).show();
									}else if(cutObjStr == '2') {
										var tmpObjId2 = 'selectImg' + cutObjStr + cutObjId;
										$(tmpObjId2).hide();
									}
								}
							}
						);				
						linkSelectMouseState = '0';
					}
				}
			}
		);
	}
}
