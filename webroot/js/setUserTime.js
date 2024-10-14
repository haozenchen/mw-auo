function setUserTime(setType, startUserId, startH, startM, userId, selectH, selectM, uselessStatus){
	if(uselessStatus == 1){
		var bgcolor = new Array('#FFCCFF', '#FF547E');
	}else{
		var bgcolor = new Array('#FFFFFF', '#FF9966');
	}
	if(startUserId == userId) {
	   var name = 'time_'+userId+'_'+selectH+':'+selectM;
	   var img = 'img_'+userId+'_'+selectH+':'+selectM;
	   var countPId = 'sum_'+selectH+':'+selectM;
	   var inputPId = 'peopleTotal_'+selectH+':'+selectM;
	   
		
		$(inputPId).value = $(countPId).innerHTML;
   	document.getElementById(name).value = setType;
   	document.getElementById(img).style.backgroundColor = bgcolor[setType];
   	document.getElementById('startUserId').value = userId;
   	document.getElementById('startH').value = selectH;
   	document.getElementById('startM').value = selectM;
//		var name = '';
//		var img = '';
//		if(parseInt(startH) > parseInt(selectH)){
//		   var tmpH = startH;
//		   var tmpM = startM;
//		   startH = selectH;
//		   startM = selectM;
//		   selectH = tmpH;
//		   selectM = tmpM;
//		}
//		for(i = parseInt(startH); i <= parseInt(selectH); i++) {
//		   if(i == parseInt(startH) && startM == '30') {
//	   		name = 'time_'+userId+'_'+i+':30';
//	   		img = 'img_'+userId+'_'+i+':30';
//		   	document.getElementById(name).value = setType;
//   			document.getElementById(img).style.backgroundColor = bgcolor[setType];
//	   	} else if(i == parseInt(selectH) && selectM == '00') {
//	   		name = 'time_'+userId+'_'+i+':00';
//	   		img = 'img_'+userId+'_'+i+':00';
//		   	document.getElementById(name).value = setType;
//   			document.getElementById(img).style.backgroundColor = bgcolor[setType];
//	   	} else {
//	   		name = 'time_'+userId+'_'+i+':00';
//			   img = 'img_'+userId+'_'+i+':00';
//		   	document.getElementById(name).value = setType;
//   			document.getElementById(img).style.backgroundColor = bgcolor[setType];
//	   		name = 'time_'+startUserId+'_'+i+':30';
//			   img = 'img_'+startUserId+'_'+i+':30';
//		   	document.getElementById(name).value = setType;
//   			document.getElementById(img).style.backgroundColor = bgcolor[setType];
//			}
//		}
   	//document.getElementById('startUserId').value = '';
   	//document.getElementById('startH').value = '';
   	//document.getElementById('startM').value = '';
	} else {
	   var name = 'time_'+userId+'_'+selectH+':'+selectM;
	   var img = 'img_'+userId+'_'+selectH+':'+selectM;
	   var countPId = 'sum_'+selectH+':'+selectM;
	   var inputPId = 'peopleTotal_'+selectH+':'+selectM;
		$(inputPId).value = $(countPId).innerHTML;
		
   	document.getElementById(name).value = setType;
   	document.getElementById(img).style.backgroundColor = bgcolor[setType];
   	document.getElementById('startUserId').value = userId;
   	document.getElementById('startH').value = selectH;
   	document.getElementById('startM').value = selectM;
   	hourSum(userId);
   }
}

function mouseOverUserTime(setType, startUserId, startH, startM, userId, selectH, selectM, uselessStatus){
	var bgcolor = new Array('#FFFFFF', '#FF9966');
	if(startUserId != '') {
	   //var name = 'time_'+startUserId+'_'+selectH+':'+selectM;
	   //var img = 'img_'+startUserId+'_'+selectH+':'+selectM;
	   var countPId = 'sum_'+selectH+':'+selectM;
	   var inputPId = 'peopleTotal_'+selectH+':'+selectM;
		
		colorChange(setType, startUserId, startH, startM, selectH, selectM, uselessStatus);
		
		$(inputPId).value = $(countPId).innerHTML;
		//document.getElementById(name).value = setType;
		//document.getElementById(img).style.backgroundColor = bgcolor[setType];
		hourSum(startUserId);
	}
}

function colorChange(setType, startUserId, startH, startM, selectH, selectM, uselessStatus) {
	if(uselessStatus == 1){
		var bgcolor = new Array('#FFCCFF', '#FF547E');
	}else{
		var bgcolor = new Array('#FFFFFF', '#FF9966');
	}

	if(parseInt(selectH) > parseInt(startH)) {
		var startI = 0;
		if(startM == '30') {
			var firstSetId = 'img_'+startUserId+'_'+startH+':'+startM;
			var firstNameId = 'time_'+startUserId+'_'+startH+':'+startM;
			document.getElementById(firstSetId).style.backgroundColor = bgcolor[setType];
			document.getElementById(firstNameId).value = setType;
			startI = parseInt(startH) + 1;
		}else{
			startI = parseInt(startH);
		}
		
		for(var i=startI; i<=parseInt(selectH); i++) {
			var tmpImg1 = 'img_'+startUserId+'_'+i+':00';
			var name1 = 'time_'+startUserId+'_'+i+':00';
			document.getElementById(tmpImg1).style.backgroundColor = bgcolor[setType];
			document.getElementById(name1).value = setType;
			
			var tmpImg2 = 'img_'+startUserId+'_'+i+':30';
			var name2 = 'time_'+startUserId+'_'+i+':30';
			document.getElementById(tmpImg2).style.backgroundColor = bgcolor[setType];
			document.getElementById(name2).value = setType;
			
			if(selectM == '00') {
				var endImg = 'img_'+startUserId+'_'+selectH+':30';
				var endName = 'time_'+startUserId+'_'+selectH+':30';
				doSetValueAndColor(setType, endImg, endName);
			}
		}
	}
	if(parseInt(selectH) < parseInt(startH)) {
		var startI = 0;
		if(startM == '00') {
			var firstSetId = 'img_'+startUserId+'_'+startH+':'+startM;
			var firstNameId = 'time_'+startUserId+'_'+startH+':'+startM;
			document.getElementById(firstSetId).style.backgroundColor = bgcolor[setType];
			document.getElementById(firstNameId).value = setType;
			startI = parseInt(startH) - 1;
		}else{
			startI = parseInt(startH);
		}
		for(var i=startI; i>=parseInt(selectH); i--) {
			var tmpImg1 = 'img_'+startUserId+'_'+i+':00';
			var name1 = 'time_'+startUserId+'_'+i+':00';
			document.getElementById(tmpImg1).style.backgroundColor = bgcolor[setType];
			document.getElementById(name1).value = setType;
			
			var tmpImg2 = 'img_'+startUserId+'_'+i+':30';
			var name2 = 'time_'+startUserId+'_'+i+':30';
			document.getElementById(tmpImg2).style.backgroundColor = bgcolor[setType];
			document.getElementById(name2).value = setType;
			
			if(selectM == '30') {
				var endImg = 'img_'+startUserId+'_'+selectH+':00';
				var endName = 'time_'+startUserId+'_'+selectH+':00';
				doSetValueAndColor(setType, endImg, endName);
			}
		}
	}
}

function doSetValueAndColor(setType, firstImg, firstName, uselessStatus) {
	if(uselessStatus == 1){
		var bgcolor = new Array('#FFCCFF', '#FF547E');
	}else{
		var bgcolor = new Array('#FFFFFF', '#FF9966');
	}

	if(setType == '1') {
		document.getElementById(firstImg).style.backgroundColor = bgcolor[0];
		document.getElementById(firstName).value = '0';
	}
	if(setType == '0') {
		document.getElementById(firstImg).style.backgroundColor = bgcolor[1];
		document.getElementById(firstName).value = '1';
	}
}

function onSelectTimeMouseUp(setType, startUserId, startH, startM, selectH, selectM) {
	//colorChange(setType, startUserId, startH, startM, selectH, selectM);
	userSum();
	hourSum(startUserId);
	$('setType').value='3';
}

function hourSum(uid) {
	var sumId = 'sum_'+uid;
	var sumAll = 'sum_all';
	var countHour = 0;
	var inputHourId = 'sumTotal_'+uid;
	var initValue = eval($(sumId).innerHTML);
	$$('td[id^=img_'+uid+']').each(
		function (el) {
			
			var objId = el.id;
			var cutPStr = el.id.split('_');
			
			if(($(objId).style.backgroundColor == 'rgb(255, 153, 102)')||($(objId).style.backgroundColor == '#FF9966')||($(objId).style.backgroundColor == '#FF547E')||($(objId).style.backgroundColor == 'rgb(255, 84, 126)')) {
				countHour += 30;
			}
			
			$(sumId).innerHTML = countHour / 60;
			var cutStr = $(sumId).innerHTML.split('.');
			if(!cutStr[1]) {
				$(sumId).innerHTML = $(sumId).innerHTML+'.0';
			}
		}
	);
	$(inputHourId).value = countHour / 60;
	$(sumAll).innerHTML = eval($(sumAll).innerHTML) + eval($(sumId).innerHTML) - initValue;
	var cutStr = $(sumAll).innerHTML.split('.');
	if(!cutStr[1]) {
		$(sumAll).innerHTML = $(sumAll).innerHTML+'.0';
	}
}

function userSum() {
	$$('td[id^=img_]').each(
		function (el) {
			var objId = el.id;
			var allObjIdCutStr = objId.split('_');
			var allTmpUserCountId = 'sum_'+allObjIdCutStr[2];
			$(allTmpUserCountId).innerHTML = 0;
		}
	);
	
	$$('td[id^=img_]').each(
		function (el) {
			var objId = el.id;
			
			if(($(objId).style.backgroundColor == 'rgb(255, 153, 102)')||($(objId).style.backgroundColor == '#FF9966')||($(objId).style.backgroundColor == '#FF547E')||($(objId).style.backgroundColor == 'rgb(255, 84, 126)')) {
				var objIdCutStr = objId.split('_');
				var tmpUserCountId = 'sum_'+objIdCutStr[2];
				$(tmpUserCountId).innerHTML = parseInt($(tmpUserCountId).innerHTML) + 1;
			}
		}
	);
}
