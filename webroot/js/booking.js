/**
 * $Id: booking.js,v 1.1 2022/07/11 02:02:41 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:41 $
 * @copyright Copyright 2007, Fonsen Technology Ltd. Corp.
 */
var schForm;
var prfShift = '';
var depUser;
var monthDays;
var rcMsg = '';

function setHolidayValue(leaveName,leaveType){
  schForm.leaveType.value = leaveType;
  schForm.leaveName.value = leaveName;  
  document.getElementById('behavior').innerHTML = "按鈕選擇："+leaveName;  
}
  function setUserValue(user,date){
    if(schForm.shift.value == "") {     
      alert("(ERR43001)請先選擇班別!");
    } else {    
      schForm.elements['data[BookingSchedule][id_'+date+'_'+user+']'].value = schForm.shift.value;
      document.getElementById('span_'+date+'_'+user).innerHTML = schForm.shift.value;
    }
  }
  
  function setOneAction(type){
    if(type == "nullValue"){
      document.getElementById('behavior').innerHTML = "按鈕選擇：清除班別"; 
    	schForm.shift.value = "";
    	schForm.nullValue.value="true";
    	schForm.leaveName.value = "";
		} else {
    	document.getElementById('behavior').innerHTML = "按鈕選擇：班別代號("+schForm.shift.value+")"; 
    	schForm.nullValue.value="";
		}
  }
  
  function setBookingValue(user,date){    
    schForm.elements['data[BookingSchedule][id_'+date+'_'+user+']'].value = schForm.shift.value;
    if(schForm.shift.value == "" && schForm.nullValue.value == "" ) { 
       alert("(ERR43001)請先選擇班別!");
    } else {
      if (schForm.leaveName.value!=''){     
        document.getElementById('span_'+date+'_'+user).innerHTML = schForm.leaveName.value;
        schForm.elements['data[BookingSchedule][leave_'+date+'_'+user+']'].value = schForm.leaveType.value;         
      }else{
        document.getElementById('span_'+date+'_'+user).innerHTML = schForm.shift.value;
        schForm.elements['data[BookingSchedule][leave_'+date+'_'+user+']'].value = "";       
      }
    }
  } 
  
  function setInputday(){
    if(schForm.elements['data[UserBearShift][beardays]'][0].checked) {
      schForm.elements['data[UserBearShift][inputDays]'].disabled='disabled';     
    }
    if(schForm.elements['data[UserBearShift][beardays]'][1].checked) {
     schForm.elements['data[UserBearShift][inputDays]'].disabled='disabled';   
    }
    if(schForm.elements['data[UserBearShift][beardays]'][2].checked) {
     schForm.elements['data[UserBearShift][inputDays]'].disabled='';   
    }    
  }
  
  function changeBearValue(){
    if(schForm.elements['data[UserBearShift][beardays]'][1].checked) {   
      document.getElementById('bearShiftValue').innerHTML="未設定";
	  }else {
		  document.getElementById('bearShiftValue').innerHTML="設定完成";  
		}   
  }
  
  function showBearDiv(evt){
	  evt = evt ? evt : (window.event ? window.event : null);   
    document.getElementById('bear').style.top=evt.clientY+20+'px';
  	document.getElementById('bear').style.left=evt.clientX+30+'px';
		Element.show('bear');   	
	}       

