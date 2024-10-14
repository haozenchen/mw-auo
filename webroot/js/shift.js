/**
 * $Id: shift.js,v 1.1 2022/07/11 02:02:42 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:42 $
 */   
/**
 * Javascript for Shift Add/Edit
 * @copyright   Copyright 2008, Fonsen Technology Ltd. Corp.
 */
function showAttOption(signIn, view){
	if(signIn == 1){
		document.getElementById(view+'_attType').disabled=false;
		document.getElementById(view+'_flexType').disabled=false;
	}else{
		document.getElementById(view+'_attType').value="";
		document.getElementById(view+'_flexType').checked=false;
		document.getElementById(view+'_attType').disabled=true;
		document.getElementById(view+'_flexType').disabled=true;
	}
}

function showOtOption(otType, view){
	if(otType == 2 || otType == 3){
		Element.show(view+'_otLimitMin');
	}else{
		Element.hide(view+'_otLimitMin');
	}
}

function chkSignIn(signIn) {
	if(signIn == 0){
		alert('此班別出勤不需打卡，無須設定出勤時段！');
		return false;
	}else{
		Element.show('periods');
		Element.hide('editDiv');
		tabClick('edit_tab2', '2');
	}
}
