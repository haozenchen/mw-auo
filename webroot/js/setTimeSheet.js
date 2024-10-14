function setTimeSheet(uid, selectH, selectM, setSelect, type){
  	colorChange(uid, selectH, selectM);
  	if(setSelect) {
  		document.getElementById('startH').value = selectH;
  		document.getElementById('selectUserId').value = uid;
  		document.getElementById('startM').value = selectM;  
  	}
  	hourSumUid(uid);
	hourSumTime(selectH, selectM, uid, type);
//	hourSumTime();    	
}

function mouseOverTimeSheet(uid, betweenMk, startH, startM, selectH, selectM){
	var startMk = (parseInt(startH, 10) * 60 * 60) + (parseInt(startM, 10) * 60);
	var selectMk = (parseInt(selectH, 10) * 60 * 60) + (parseInt(selectM, 10) * 60);
	if(startMk > selectMk) {
		var tmpMk = startMk;
		startMk = selectMk;
		selectMk = tmpMk;
	}
	while(startMk <= selectMk) {
		var tmpH = Math.floor(startMk / 60 / 60);
		var tmpM = Math.floor((startMk - (tmpH * 60 * 60)) / 60);
		var strH = String(tmpH);
		var strM = String(tmpM);
		if(tmpH < 10) { strH = '0'+strH;}
		if(tmpM < 10) { strM = '0'+strM;}
		colorChange(uid, strH, strM);
		startMk = startMk + parseInt(betweenMk, 10);
	}
	hourSumUid(uid);
	hourSumTime(selectH, selectM, uid, 'over');
//	hourSumTime();
}

document.utf8Substr = function (str, startChr, len) {
	if(!str || !len) { 
		return ''; 
	}
	var num = 0;
	var temp = '';
	for (var i=startChr;i<str.length;i++) {
		if (str.charCodeAt(i)>255) {
			num+=2;
		} else {
			num++;
		}
		if(num > len) { 
			return temp; 
		}
		temp += str.charAt(i);
	}
	return str; 
};

function colorChange(uid, selectH, selectM) {
	var setType = document.getElementById('setType').value;
	var shiftGroupSn = document.getElementById('shiftGroupSn').value;
	var shiftGroupId = document.getElementById('shiftGroupId').value;
	
   var name = 'time_'+uid+'_'+selectH+':'+selectM;
   var img = 'img_'+uid+'_'+selectH+':'+selectM;
   var sn = 'sn_'+uid+'_'+selectH+':'+selectM;
   var ot = 'ot_'+uid+'_'+selectH+':'+selectM;

  	document.getElementById(name).value = shiftGroupId;
  	document.getElementById(img).style.backgroundColor = document.selectColor[setType];
  	document.getElementById(sn).innerHTML = document.utf8Substr(shiftGroupSn, 0, 4);
  	document.getElementById(sn).title = shiftGroupSn;
  	document.getElementById(ot).value = setType;
}

function onSelectTimeMouseUp() {
	$('selectUserId').value='';
	$('startH').value='';
	$('startM').value='';
}

function hourSumUid(uid) {
	var sumAttId = 'sumAtt_'+uid;
	var sumOtId = 'sumOt_'+uid;
	var sumId = 'sumAll_'+uid;
	var attHour = 0;
	var otHour = 0;
  	var betweenMk = parseInt(document.getElementById('betweenMk').value);	
	$$('td[id^=img_'+uid+'_]').each(
		function (el) {
			var objId = el.id;
			var cutPStr = el.id.split('_');
			if(($(objId).style.backgroundColor == 'rgb(255, 153, 102)')||($(objId).style.backgroundColor.toUpperCase() == '#FF9966')) {
				attHour += betweenMk;
			} else {
				if(($(objId).style.backgroundColor == 'rgb(92, 194, 78)')||($(objId).style.backgroundColor.toUpperCase() == '#5CC24E')) {
					otHour += betweenMk;
				}			
			}

			$(sumAttId).innerHTML = Math.floor((attHour / 60 / 60) * 100) / 100;
			$(sumOtId).innerHTML = Math.floor((otHour / 60 / 60) * 100) / 100;
			$(sumId).innerHTML = parseFloat($(sumAttId).innerHTML) + parseFloat($(sumOtId).innerHTML);
		}
	);
}

function hourSumTime(selectH, selectM, uid, type) {
//function hourSumTime() {
  	var betweenMk = parseInt(document.getElementById('betweenMk').value);
	var betweenH = (Math.floor(((betweenMk) / 60 / 60) * 100) / 100);
  	var sumAllId = 'sumAll';
  	var sumAttId = 'sumAtt';
  	var sumOtId = 'sumOt';
  	
	$(sumAllId).innerHTML = 0;
	$(sumAttId).innerHTML = 0;
	$(sumOtId).innerHTML = 0;
 	
	/*$$('td[id^=img_]').each(
		function (el) {
			var objId = el.id;
			var allObjIdCutStr = objId.split('_');
			var time = allObjIdCutStr[2];
			var allTmpUserCountId = 'sum_'+time;
			$(allTmpUserCountId).innerHTML = 0;
		}
	);*/
	
	if(type == 'set') {
		if($('tmpSelectAllUser').value == '1') {
			$('sum_'+selectH+':'+selectM).innerHTML = 0;
			$('tmpSelectAllUser').value = '0'
		}
		
		$('sum_'+selectH+':'+selectM).innerHTML = parseFloat($('sum_'+selectH+':'+selectM).innerHTML) + betweenH;
		/*$$('td[id$=_'+selectH+':'+selectM+']').each(
			function (el) {
				var objId = el.id;
				var allObjIdCutStr = objId.split('_');
				var time = allObjIdCutStr[2];
				var allTmpUserCountId = 'sum_'+time;
//				if($(allTmpUserCountId).innerHTML == '') {
					$(allTmpUserCountId).innerHTML = 0;
//				}
			}
		);
		
		$$('td[id$=_'+selectH+':'+selectM+']').each(
			function (el) {
				var objId = el.id;
				var objIdCutStr = objId.split('_');
				var time = objIdCutStr[2];
				var tmpUserCountId = 'sum_'+time;
				
				if(($(objId).style.backgroundColor == 'rgb(255, 153, 102)')||($(objId).style.backgroundColor.toUpperCase() == '#FF9966')) {
					$(tmpUserCountId).innerHTML = parseFloat($(tmpUserCountId).innerHTML) + betweenH;
					//$(sumAllId).innerHTML = parseFloat($(sumAllId).innerHTML) + betweenH;
					//$(sumAttId).innerHTML = parseFloat($(sumAttId).innerHTML) + betweenH;
				} else {
					if(($(objId).style.backgroundColor == 'rgb(92, 194, 78)') || ($(objId).style.backgroundColor.toUpperCase() == '#5CC24E')) {
						$(tmpUserCountId).innerHTML = parseFloat($(tmpUserCountId).innerHTML) + betweenH;
						//$(sumAllId).innerHTML = parseFloat($(sumAllId).innerHTML) + betweenH;
						//$(sumOtId).innerHTML = parseFloat($(sumOtId).innerHTML) + betweenH;
					}
				}
			}
		);*/
	} else {
		//$$('td[id^=img_'+uid+'_]').each(
		$$('td[id^=img_]').each(
			function (el) {
				var objId = el.id;
				var allObjIdCutStr = objId.split('_');
				var time = allObjIdCutStr[2];
				var allTmpUserCountId = 'sum_'+time;
			//	if($(allTmpUserCountId).innerHTML == '') {
					$(allTmpUserCountId).innerHTML = 0;
		//		}
			}
		);
		
		//$$('td[id^=img_'+uid+'_]').each(
		$$('td[id^=img_]').each(
			function (el) {
				var objId = el.id;
				var objIdCutStr = objId.split('_');
				var time = objIdCutStr[2];
				var tmpUserCountId = 'sum_'+time;
				if(($(objId).style.backgroundColor == 'rgb(255, 153, 102)')||($(objId).style.backgroundColor.toUpperCase() == '#FF9966')) {
					$(tmpUserCountId).innerHTML = parseFloat($(tmpUserCountId).innerHTML) + betweenH;
					//$(sumAllId).innerHTML = parseFloat($(sumAllId).innerHTML) + betweenH;
					//$(sumAttId).innerHTML = parseFloat($(sumAttId).innerHTML) + betweenH;
				} else {
					if(($(objId).style.backgroundColor == 'rgb(92, 194, 78)') || ($(objId).style.backgroundColor.toUpperCase() == '#5CC24E')) {
						$(tmpUserCountId).innerHTML = parseFloat($(tmpUserCountId).innerHTML) + betweenH;
						//$(sumAllId).innerHTML = parseFloat($(sumAllId).innerHTML) + betweenH;
						//$(sumOtId).innerHTML = parseFloat($(sumOtId).innerHTML) + betweenH;
					}
				}
			}
		);
	}
	
	var tmpSumAtt = 0;
	var tmpSumOt = 0;
	var tmpSumAll = 0;
	
	$$('td[id^=sumAtt_]').each(
		function (el) {
			tmpSumAtt = parseInt(tmpSumAtt) + parseInt(parseFloat($(el.id).innerHTML) * 100);
		}
	);
	$(sumAttId).innerHTML = parseFloat(tmpSumAtt/100);
	
	$$('td[id^=sumOt_]').each(
		function (el) {
			tmpSumOt = parseInt(tmpSumOt) + parseInt(parseFloat($(el.id).innerHTML) * 100);
		}
	);
	$(sumOtId).innerHTML = parseFloat(tmpSumOt/100);
	
	$$('td[id^=sumAll_]').each(
		function (el) {
			tmpSumAll = parseInt(tmpSumAll) + parseInt(parseFloat($(el.id).innerHTML) * 100);
		}
	);
	$(sumAllId).innerHTML = parseFloat(tmpSumAll/100);

	/*$$('td[id^=img_]').each(
		function (el) {
			var objId = el.id;
			var objIdCutStr = objId.split('_');
			var time = objIdCutStr[2];
			var tmpUserCountId = 'sum_'+time;
			
			if(($(objId).style.backgroundColor == 'rgb(255, 153, 102)')||($(objId).style.backgroundColor.toUpperCase() == '#FF9966')) {
				$(tmpUserCountId).innerHTML = parseFloat($(tmpUserCountId).innerHTML) + betweenH;
				$(sumAllId).innerHTML = parseFloat($(sumAllId).innerHTML) + betweenH;
				$(sumAttId).innerHTML = parseFloat($(sumAttId).innerHTML) + betweenH;
			} else {
				if(($(objId).style.backgroundColor == 'rgb(92, 194, 78)') || ($(objId).style.backgroundColor.toUpperCase() == '#5CC24E')) {
					$(tmpUserCountId).innerHTML = parseFloat($(tmpUserCountId).innerHTML) + betweenH;
					$(sumAllId).innerHTML = parseFloat($(sumAllId).innerHTML) + betweenH;
					$(sumOtId).innerHTML = parseFloat($(sumOtId).innerHTML) + betweenH;
				}
			}
		}
	);*/
}
