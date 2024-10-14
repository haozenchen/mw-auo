/**
 * $Id: schedule.js,v 1.1 2022/07/11 02:02:42 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:42 $
 * @copyright Copyright 2007, Fonsen Technology Ltd. Corp.
 */
/**
 * Javascript library of schedule
 */      
var schForm;
var prfShift = '';
var depUser;
var monthDays;
var rcMsg = '';
var depShift;
var nullshift;
beforeDay = 0;
function setHolidayValue(leaveName,leaveType){
  schForm.leaveType.value = leaveType;
  schForm.leaveName.value = leaveName;
  schForm.dutyName.value = "";  
  schForm.dutyType.value = "";
  document.getElementById('behavior').innerHTML = "按鈕選擇："+leaveName;  
}

 
function clearDuty(){
  schForm.dutyType.value = "";
  schForm.dutyName.value = "clear";  
  schForm.leaveType.value = "";
  schForm.leaveName.value = "";
  schForm.shift.value= "";   
  document.getElementById('behavior').innerHTML = "按鈕選擇：解除值勤";  
}
  
function chgColor(obj,date,user) {
  if (typeof schForm.elements['data[Schedule][duty_'+date+'_'+user+']'] != 'undefined' && schForm.elements['data[Schedule][duty_'+date+'_'+user+']'].value != ""){
    document.getElementById('span_'+date+'_'+user).style.textDecoration='underline';
  }else {
    document.getElementById('span_'+date+'_'+user).style.textDecoration='';
   }
}

function divposition(user,date,usershiftID){
  document.getElementById('dutyDetail').style.top=window.event.y-30+'px';
  document.getElementById('dutyDetail').style.left=window.event.x+15+'px';
  if (typeof schForm.elements['data[Schedule][duty_'+date+'_'+user+']'] != 'undefined' && schForm.elements['data[Schedule][duty_'+date+'_'+user+']'].value != ""){
    Element.show('dutyDetail');
    html = "使用者:"+user+"<br>日期"+date+"<br>名稱:"+schForm.elements['data[Schedule][dutyHName_'+date+'_'+user+']'].value;
    Element.update('dutyDetailContent', html);
  }else {
    Element.hide('dutyDetail'); 
  }       
}  


function showDutyDetail(user,date, evt){
	evt = evt ? evt : (window.event ? window.event : null);   
  document.getElementById('dutyDetail').style.top=evt.clientY-30+'px';
  document.getElementById('dutyDetail').style.left=evt.clientX+15+'px';
  if (typeof schForm.elements['data[Schedule][duty_'+date+'_'+user+']'] != 'undefined' && schForm.elements['data[Schedule][duty_'+date+'_'+user+']'].value != ""){
    Element.show('dutyDetail');
    test = document.getElementById('name_'+date+'_'+user).value;
    html = "使用者:"+test+"<br>日期"+date+"<br>名稱:"+schForm.elements['data[Schedule][dutyHName_'+date+'_'+user+']'].value;
    Element.update('dutyDetailContent', html);
  }else {
    Element.hide('dutyDetail'); 
  }       
}  

function setOneActions(shiftValue,dutyName,dutyType,type){
    if (type == "shiftCode") {
      schForm.shift.value = shiftValue;
      schForm.leaveName.value = "";
      schForm.leaveType.value = "";
      schForm.dutyName.value = "";  
      schForm.dutyType.value = "";
      schForm.nullValue.value="";
      document.getElementById('behavior').innerHTML = "按鈕選擇：班別代號("+shiftValue+")"; 
    } else if(type == "nullValue"){
      schForm.leaveName.value = "";
      schForm.leaveType.value = "";
      schForm.shift.value = "";  
      schForm.dutyName.value = "";  
      schForm.dutyType.value = "";
      schForm.nullValue.value="true";
      document.getElementById('behavior').innerHTML = "按鈕選擇：清除班別"; 
    } else {     
      schForm.dutyName.value = dutyName;  
      schForm.dutyType.value = dutyType;
      schForm.leaveName.value = "";
      schForm.leaveType.value = "";
      schForm.shift.value = "";
      schForm.nullValue.value="";
      document.getElementById('behavior').innerHTML = "按鈕選擇："+dutyName+"(值勤)";  
    }
 } 

 function choiceDay(date,lastday){  
   if(beforeDay == 0 || beforeDay == date) {
	    document.getElementById('fields_'+date).style.backgroundColor= "#FF7256";
	    for (var j=0;j< nullshift.length; j++ ) { //shift's code
        document.getElementById(date+'_'+nullshift[j]).style.backgroundColor = "#FFEC8B";  
      }
      beforeDay = date;
	 }else {
	    document.getElementById('fields_'+date).style.backgroundColor= "#FF7256";
	    document.getElementById('fields_'+beforeDay).style.backgroundColor = "#a0aedb";
	    for (var j=0;j< nullshift.length; j++ ) { //shift's code
        document.getElementById(date+'_'+nullshift[j]).style.backgroundColor = "#FFEC8B";
        document.getElementById(beforeDay+'_'+nullshift[j]).style.backgroundColor = ""; 
      }
			beforeDay = date;  
	 }  
  }  
 
function setUserValue(user,date){   
	scheduleId = schForm.elements['data[Schedule][' + prfShift + '_' +date+'_'+user+']'];   
	leave = schForm.elements['data[Schedule][leave_'+date+'_'+user+']'];
	duty = schForm.elements['data[Schedule][duty_'+date+'_'+user+']'];
	dutyHName = schForm.elements['data[Schedule][dutyHName_'+date+'_'+user+']'];
	if (schForm.leaveName.value!=''){        
		scheduleId.value = schForm.shift.value;        
		setScheduleData(date,user,schForm.shift.value);
		setLeaveData(date,user,schForm.leaveType.value);
		document.getElementById('span_'+date+'_'+user).innerHTML = schForm.leaveName.value;     
		leave.value = schForm.leaveType.value;
		duty.value = "";
		dutyHName.value = "";      
	}else{

		if (schForm.dutyType.value != "") {
			var userShiftCode = scheduleId.value;    
			var userLeaveCode = leave.value;
			if (userShiftCode != '' && userLeaveCode == '' ){
				duty.value = schForm.dutyType.value; 
				dutyHName.value = schForm.dutyName.value;
			} else {
				if(userLeaveCode != "") {
					alert("(ERR31003)當天請假，無法值勤");
				}
				if(userShiftCode == "") {
					alert("(ERR31002)值勤當天需先排班，請先填入班別");
				}
				duty.value = ""; 
				dutyHName.value = "";      
			}
		}else{   
			if (schForm.dutyName.value == "clear") { // unClick duty code
				duty.value = ""; 
				dutyHName.value = "";    
			}else {          
				if ((schForm.shift.value == "") && (schForm.nullValue.value == "") ){
					alert("(ERR31001)請先選擇班別");
				}else {
					scheduleId.value = schForm.shift.value;           
					setScheduleData(date,user,schForm.shift.value);
					document.getElementById('span_'+date+'_'+user).innerHTML = schForm.shift.value;  
					leave.value = ""; 
					duty.value = ""; 
					dutyHName.value = "";  
				}
			}
		}  
	}   
}
  
var temp;
var total = 0;


function setScheduleData(day,uid,shiftCode){  
  schedule['id_'+day+ '_' + uid] = shiftCode;  
}

function setLeaveData(day,uid,leaveTypeID){
  leaveType['leave_'+day+'_'+uid] = leaveTypeID; 
}

function getShift(day,uid){
  var shiftValue;
  shiftValue = schedule['id_'+day+ '_' + uid];  
  if (shiftValue == ''){  
    return 'X';
  }else { 
    return shiftValue;    
  } 
  return false;
}

function getLeaveType(day,uid){ 
  var leaveTypeValue;
  leaveTypeValue = leaveType['leave_' + day + '_' + uid];
  if (leaveTypeValue == ''){  
    return 'X';
  }else { 
    return leaveTypeValue;    
  } 
  return false;    
}

function ruleCheck(day,uid,userName){ 
  rcMsg = '';
  Element.update('checkrule',rcMsg);
  hrCount(day,uid);
  Element.update('checkrule',rcMsg);
  sixDays(uid,userName);
  Element.update('checkrule',rcMsg);
  userShiftCount(uid);
  countLeaveHour(uid);  
 // dayLeaveHour(day);
}

function countLeaveHour(uid){
  var LTPId = '';
  var LTPDay = '';
  var shiftCode = '';
  var leave_state = new Hash();
  leave_state[uid] = new Hash();
  for (day = 0; day < monthDays.length; day++){
     shiftCode = getShift(monthDays[day],uid);   
		 LTPId = getLeaveType(monthDays[day],uid);	
		 if(LTPId != 'X' && leave_hour[shiftCode] != null){			 	
		   if( leave_state[uid][LTPId] == null){
			   leave_state[uid][LTPId] = leave_hour[shiftCode];  
			 } else {	  		
		 	   leave_state[uid][LTPId] += leave_hour[shiftCode];
		   }		   
		 }			  
	}
  
	for (var j=0;j<  leave_id.length; j++ ) {
		if( leave_state[uid][leave_id[j]] != null) {
		  document.getElementById('ltpId_'+uid+'_'+leave_id[j]).innerHTML = leave_state[uid][leave_id[j]];
		}else {
		  document.getElementById('ltpId_'+uid+'_'+leave_id[j]).innerHTML = '';
		}		
	}	   
   
  return false;
}

function dayLeaveHour(day){ 
  var LTPId = '';
  var LTPDay = '';
  var shiftCode = '';
  var leave_state = new Hash();
  leave_state[day] = new Hash();
	for (user=0 ; user < depUser.length; user++) {
	  shiftCode = getShift(day,depUser[user]);	 
	  LTPId = getLeaveType(day,depUser[user]);		
	  if(LTPId != 'X' && leave_hour[shiftCode] != null){		 	
		  if( leave_state[day][LTPId] == null){		   
				leave_state[day][LTPId] = leave_hour[shiftCode];  
			} else {	  			  		
		 		leave_state[day][LTPId] += leave_hour[shiftCode];
		  }		   
	  }	     
	}  
	for (var j=0;j<  leave_id.length; j++ ) {
		if( leave_state[day][leave_id[j]] != null) {
		  document.getElementById('dayLtpId_'+day+'_'+leave_id[j]).innerHTML = leave_state[day][leave_id[j]];
		}else {
		  document.getElementById('dayLtpId_'+day+'_'+leave_id[j]).innerHTML = '';
		}		
	}	  
  return false;  
}




function sixDays(uid,userName){ 
	var shfitCode = '';
	var theDay = '';  
	for (day=0; day < monthDays.length-6; day++ ) {
		var attendance = 0;    
		for (sixday = day; sixday <= day+6; sixday++ ){      
			theDay = monthDays[sixday];    
			shiftCode = getShift(theDay,uid);       
			if (shiftCode != 'O' && shiftCode != 'X') {       
				attendance ++;      
			}         
		}     
		if (attendance > 6) {
			rcMsg = rcMsg + '<BR>' + userName + '已連續工作超過六日';          
			return false;
		}      
	}  
	return false;
}

function userShiftCount(uid){
	var shiftCode = '';
	var userShift = new Hash();
	userShift[uid] = new Hash();
	for (day=0; day < monthDays.length; day++ ) {
	  shiftCode = getShift(monthDays[day],uid);
	  if (shiftCode != 'X') {	
	    key = shiftCode;
			if( userShift[uid][key] == null){
			  userShift[uid][key]  =  1;
			}else {
			  userShift[uid][key] = userShift[uid][key] + 1;
			} 
		}		    
	}
	
	  for (var j=0;j< nullshift.length; j++ ) {	   
       if( userShift[uid][nullshift[j]] != null) {
          //alert('222');
         document.getElementById('user_'+uid+'_'+nullshift[j]).innerHTML = userShift[uid][nullshift[j]];
       }else {
         document.getElementById('user_'+uid+'_'+nullshift[j]).innerHTML = '';
       }                    
    }
		 
    return false;
	//alert(userShift['1']['D']);
	//alert(monthDays[1]);
  //for(var i=1; i<= lastday; i++ ) {
	//	shiftCode = getShift(day,uid);  
//	} 
}



function hrCount(day,uid){
	var countUser = new Hash();
	var user = 0;
	var shiftCode = '';
	for (user=0 ; user < depUser.length; user++) {
		uid = depUser[user];    
		shiftCode = getShift(day,uid);
		if (shiftCode != 'X') {
			key = shiftCode;
			if (countUser[key] == null) {
				countUser[key] = 1;
			} else {
				countUser[key] = countUser[key] + 1;
			}   						  
		}
	}

	rcMsg = '當天('+day+'): ';
	countUser.each(function(pair) {
			if (depShift[pair.key] != null) {
			rcMsg = rcMsg + pair.value+ ' 位' + depShift[pair.key] + '/ ';
			}    
			}     
		      );

	for (var j=0;j< nullshift.length; j++ ) {
		if( countUser[nullshift[j]] != null) {
			document.getElementById(day+'_'+nullshift[j]).innerHTML = countUser[nullshift[j]];
		}else {
			document.getElementById(day+'_'+nullshift[j]).innerHTML = '';
		}                    
	}   
	return false;  
}

function people_powers(count){
  if(schForm.manpowers.checked) {
  	for (var i=0;i<=count;i++ ) {
     Element.show('hidden_'+i);
    }
    Element.show('hidden_x');
  }else {
  	for (var i=0;i<=count;i++ ) {
     Element.hide('hidden_'+i);
    }
    Element.hide('hidden_x');
  }
}

//暫存班別代碼於常用班表頁面
var arr = new Array();
var strHtml = "";
function AddShiftsButton(id,code,timecount,timevalue,totalhours)
{
  var check = "0";
  for(var i=0;i<arr.length;i++)
  {
    if(code == arr[i])
    {
      document.getElementById('showAddArea').innerHTML = "can't";
      check = "1";
    }
  }
  if(check == "0")
  {
    arr[arr.length] = code;
    strHtml += "<span class='schBtn' flag='schBtn' onmouseover=JavaScript:ShowOtherShiftsInfo('"+code+"','"+timecount+"','"+timevalue+"','"+totalhours+"',event) onmouseout=JavaScript:cancelShowShiftsInfo(event) onclick=JavaScript:schBtnClick('"+id+"','"+code+"','"+timecount+"','"+timevalue+"','"+totalhours+"',event) >"+code+"</span>&nbsp";
  }
  document.getElementById('showAddArea').innerHTML = strHtml;
}

function schBtnClick(id,code,timecount,timevalue,totalhours, event)
{

	$$('span[flag=schBtn]').each(function (e) {e.setStyle({ borderColor:'#999999', color:'#999999', background:'#FFFFFF'}); });

	var getObj = event.srcElement? event.srcElement : event.target;
	$(getObj).style.borderColor = '#333333';
	$(getObj).style.color = '#333333';
	$(getObj).style.background = '#FFF68F';
	
	var cutStr = '';
	cutStr = timevalue.split('&&And&&');
	
	var htmlStr = '';
	htmlStr += '班別：'+code;
	htmlStr += '&nbsp||&nbsp總時數：'+totalhours+'<br>時段：';
	for(var i=0; i<timecount; i++) {
		htmlStr += cutStr[i].substring(0,5)+ cutStr[i].substring(8,9) + cutStr[i].substring(9,14) + '&nbsp&nbsp';
	}
	
	$('flashMessage').innerHTML='<div id=clickMsg class=passMsg>'+htmlStr+'</div><div id=moveMsg style=color:red class=passMsg></div>';
	
	document.getElementById("startShift").value = 1;
	document.getElementById("selectedShift").value = code;
	document.getElementById("selectedShiftId").value = id;
}

var shiftsDivFirstCreate = '0';

function ShowOtherShiftsInfo(code,timecount,timevalue,totalhours,event)
{
	var getObj = event.srcElement? event.srcElement : event.target;
	getObj.className='schBtn_hover';
	
	var cutStr = '';
	var cutStr = timevalue.split('&&And&&');

	$('flashMessage').style.width = '500px';
	
	var htmlStr = '';
	htmlStr += '班別：'+code;
	htmlStr += '&nbsp||&nbsp總時數：'+totalhours+'<br>時段：';
	for(var i=0; i<timecount; i++) {
		htmlStr += cutStr[i].substring(0,5)+ cutStr[i].substring(8,9) + cutStr[i].substring(9,14) + '&nbsp&nbsp';
	}
	
	$('flashMessage').style.display = 'block';

	if($('moveMsg')) {
		$('moveMsg').innerHTML = htmlStr;
	}else{
		$('flashMessage').innerHTML = '<div id="moveMsg" style="color:red" class="passMsg">'+htmlStr+'</div>';
	}
	
	if($('clickMsg')) {
		$('clickMsg').style.display = 'none';
	}
}

function cancelShowShiftsInfo(event) {
	var getObj = event.srcElement? event.srcElement : event.target;
	getObj.className='schBtn';
	$('moveMsg').innerHTML = '';
	
	if($('clickMsg')) {
		$('clickMsg').style.display = 'block';
	}
	
}

//滑鼠右鍵快捷選單功能
var isShowMenu=false;
function showmenu(e)
{
	$$('table[id^=rightMenuAnd]').each(
		function (el) {
			if(el) {
				var tagId = el.id;
				$(tagId).style.visibility = 'hidden';
			}
		}
	);
   if (!e) {var e = window.event;}
   var getObj = e.srcElement? e.srcElement : e.target;
   var cutGetObjStr = getObj.id.split("And");
   var cbStr = "";
   cbStr = "rightMenuAnd" + cutGetObjStr[1];
   
   var menuObj=document.getElementById(cbStr);
	if(isShowMenu)
	{
      
      if (!e) {var e = window.event;}
      menuObj.style.left = e.clientX + document.documentElement.scrollLeft + "px";
		menuObj.style.top = e.clientY + document.documentElement.scrollTop + "px";
      
      menuObj.style.visibility="visible";
		isShowMenu=false;
		return false;
	}
}

function closemenu(id)
{
   var cbStr = "rightMenuAnd" + id;
   document.getElementById(cbStr).style.visibility="hidden";
}

function rightMenuClick(e)
{
	var rightCheck;
	
   if (!e) {var e = window.event;}
	if (e.which)
	{
		rightCheck = e.which;
	}else if (e.button)
	{
		rightCheck = e.button;
	}

	if(rightCheck)
	{
		isShowMenu=true;
		document.oncontextmenu=showmenu;
	}
}
