// JavaScript Document

var $j = jQuery.noConflict();

var syspath = new Array('femas', 'test', 'demo', 'femas_service', 'mysql', 'saas', 'saas_init_db');


function regainTrial(){
  if($j("#email").val() == '' || $j("#email").val() == '郵件信箱(註冊時填寫的email)'|| $j("#email").val() == '' || $j("#email").val() == '　'){
    alert('郵件信箱為必填'); $j("#email").css("color","red");
    return false;
  }else if(!validate($j("#email").val())){
    alert('郵件信箱格式錯誤'); $j("#email").css("color","red");
    return false;
  }else if($j("#sn").val() == '' || $j("#sn").val() == '您的專屬路徑(註冊時申請的專屬路徑)'|| $j("#sn").val() == '' || $j("#sn").val() == '　'){
    alert('專屬路徑為必填'); $j("#sn").css("color","red");
    return false;
  }else if(in_array($j("#sn").val(), syspath)){
    alert('此路徑為系統使用，請選擇其他名稱'); $j("#sn").css("color","red");
    return false;
  }else {
    return true;
  }
}

// validate email
function validate(address) {
  var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
  if(reg.test(address) == false) {
    return false;
  }else{
	  return true;
  }
}

function checkValSn( str ) {
  var regExp = /^[\d|a-zA-Z]+$/;
  if (regExp.test(str)){
    str = str.toLowerCase();
    return str;
  }else if(str == '您的專屬路徑(限英文及數字)' || str == '系統預設帳號'){
    return str;
  }else{
    alert('只能由英文及數字組成');
    return '';
  }
}

function in_array(s, a) {
  for (k=0; v=a[k]; k++) {
    if (v==s) {
      return true;
    }
  }
  return false;
}