/**
 * $Id: menu.js,v 1.1 2022/07/11 02:02:42 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:42 $
 */   
/**
 * Javascript for Top Menu
 * @copyright   Copyright 2007, Fonsen Technology Ltd. Corp.
 */
function MenuOn(x){ 
	obj = document.getElementById("submenu_" + x).style.visibility="visible";
}

function MenuOff(x){ 
	obj = document.getElementById("submenu_" + x).style.visibility="hidden"; 
}

function sideMenuCategory(category) {
	if($('menuBlock_' + category).style.display == 'none') {
		$$('span[tag=category]').each(function (el) { el.getElementsByTagName('img')[0].src='/emma/img/menuClose.gif'; } );
		$$('div[tag=menuBlock]').each(function (el) { Element.hide(el); });
		Element.show('menuBlock_' + category);
		$('category_' + category).getElementsByTagName('img')[0].src='/emma/img/menuOpen.gif';
	} else { 
		Element.hide('menuBlock_' + category);
		$('category_' + category).getElementsByTagName('img')[0].src='/emma/img/menuClose.gif';
	}
}

function sideMenuItem(category, item) {
	$('menuItem_' + category + '_' + item).addClassName('click');
	Cookie.set('menuBlock', category);
	Cookie.set('menuItem', item);
}

function onloadSideMenu(initK, menuBlockKey, menuItemKey) {
	var menuBlock = menuBlockKey;
	var menuItem = menuItemKey;
	if($('menuBlock_' + menuBlock) == null) { 
		menuBlock = initK; menuItem = 0; 
	} else { 
		if(menuItem == '' || menuItem == null) {
			menuItem = 0;
		} 
	} 
	Element.show('menuBlock_' + menuBlock);
	$('menuItem_' + menuBlock + '_' + menuItem).addClassName('click');	
	$('category_' + menuBlock).getElementsByTagName('img')[0].src='/emma/img/menuOpen.gif';
}

/**
 * block parent element's event
 */
function blockPopMenuEvent(event) {
	if (!event) {
		event = window.event;	// IE
	}
	if (event.stopPropagation) {
		event.stopPropagation();	// standard, but IE6 not support
	} else {
		event.cancelBubble = true;
	}
}
