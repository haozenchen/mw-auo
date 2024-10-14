// JavaScript Document

var $j = jQuery.noConflict();

var syspath = new Array('fonsen', 'femas', 'saas', 'saas_init_db', 'femas_service', 'demo', 'mysql', 'test', 'information_schema', 'fsdbmanager', 'phpmyadmin', 'admin', 'administrator', 'models', 'views', 'persistent', 'tmp', 'imgs');

var userNumArray = new Array();
userNumArray['0'] = '10';
userNumArray['1'] = '20';
userNumArray['2'] = '50';
userNumArray['3'] = '80';
userNumArray['4'] = '100';
userNumArray['5'] = '200';
userNumArray['6'] = '300';
userNumArray['7'] = '400';
userNumArray['8'] = '500';
userNumArray['9'] = '600';
userNumArray['10'] = '700';
userNumArray['11'] = '800';
userNumArray['12'] = '900';
userNumArray['13'] = '1000';
userNumArray['14'] = '2000';

var uNumArray = new Array();
uNumArray['0'] = '10';
uNumArray['1'] = '20';
uNumArray['2'] = '50';
uNumArray['3'] = '80';
uNumArray['4'] = '100';
uNumArray['5'] = '200';
uNumArray['6'] = '300';
uNumArray['7'] = '400';
uNumArray['8'] = '500';
uNumArray['9'] = '1000';
uNumArray['10'] = '2000';

var auNumArray = new Array();
auNumArray['0'] = '10';
auNumArray['1'] = '20';
auNumArray['2'] = '50';
auNumArray['3'] = '80';
auNumArray['4'] = '100';
auNumArray['5'] = '200';
auNumArray['6'] = '300';
auNumArray['7'] = '400';
auNumArray['8'] = '500';
auNumArray['9'] = '1000';
auNumArray['10'] = '2000';

var fnNameArray = new Array();
fnNameArray['AD'] = '差勤+簽核';
fnNameArray['ACD'] = '差勤+簽核+排班';
fnNameArray['ABD'] = '差勤+薪資+簽核';
fnNameArray['ABC'] = '差勤+排班+薪資';
fnNameArray['ABCD'] = '差勤+簽核+排班+薪資';
fnNameArray['ABCDG'] = '差勤+簽核+排班+薪資+考核';
fnNameArray['ABCDGI'] = '差勤+簽核+排班+薪資+考核+電子表單';
fnNameArray['ABCDGFI'] = '差勤+簽核+排班+薪資+考核+培訓+電子表單';
fnNameArray['A'] = '差勤';
fnNameArray['AC'] = '差勤+排班';

var fnPriceArray = new Array();
fnPriceArray['AD_10'] = 390;
fnPriceArray['AD_20'] = 679;
fnPriceArray['AD_50'] = 1599;
fnPriceArray['AD_80'] = 2399;
fnPriceArray['AD_100'] = 2698;
fnPriceArray['AD_200'] = 4169;
fnPriceArray['AD_300'] = 5328;
fnPriceArray['AD_400'] = 6398;
fnPriceArray['AD_500'] = 7568;
fnPriceArray['AD_600'] = 8468;
fnPriceArray['AD_700'] = 9228;
fnPriceArray['AD_800'] = 9919;
fnPriceArray['AD_900'] = 10498;
fnPriceArray['AD_1000'] = 11349;
fnPriceArray['AD_2000'] = 17078;
fnPriceArray['ACD_10'] = 420;
fnPriceArray['ACD_20'] = 729;
fnPriceArray['ACD_50'] = 1719;
fnPriceArray['ACD_80'] = 2579;
fnPriceArray['ACD_100'] = 2898;
fnPriceArray['ACD_200'] = 4489;
fnPriceArray['ACD_300'] = 5718;
fnPriceArray['ACD_400'] = 6848;
fnPriceArray['ACD_500'] = 8073;
fnPriceArray['ACD_600'] = 9003;
fnPriceArray['ACD_700'] = 9788;
fnPriceArray['ACD_800'] = 10499;
fnPriceArray['ACD_900'] = 11098;
fnPriceArray['ACD_1000'] = 11999;
fnPriceArray['ACD_2000'] = 17948;
fnPriceArray['ABD_10'] = 589;
fnPriceArray['ABD_20'] = 999;
fnPriceArray['ABD_50'] = 2299;
fnPriceArray['ABD_80'] = 3399;
fnPriceArray['ABD_100'] = 3758;
fnPriceArray['ABD_200'] = 5789;
fnPriceArray['ABD_300'] = 7258;
fnPriceArray['ABD_400'] = 8598;
fnPriceArray['ABD_500'] = 9988;
fnPriceArray['ABD_600'] = 11068;
fnPriceArray['ABD_700'] = 11987;
fnPriceArray['ABD_800'] = 12858;
fnPriceArray['ABD_900'] = 13637;
fnPriceArray['ABD_1000'] = 14648;
fnPriceArray['ABD_2000'] = 21077;
fnPriceArray['ABC_10'] = 399;
fnPriceArray['ABC_20'] = 679;
fnPriceArray['ABC_50'] = 1519;
fnPriceArray['ABC_80'] = 2179;
fnPriceArray['ABC_100'] = 2359;
fnPriceArray['ABC_200'] = 3579;
fnPriceArray['ABC_300'] = 4249;
fnPriceArray['ABC_400'] = 4849;
fnPriceArray['ABC_500'] = 5454;
fnPriceArray['ABC_600'] = 5844;
fnPriceArray['ABC_700'] = 6238;
fnPriceArray['ABC_800'] = 6549;
fnPriceArray['ABC_900'] = 6898;
fnPriceArray['ABC_1000'] = 7298;
fnPriceArray['ABC_2000'] = 8898;
fnPriceArray['ABCD_10'] = 619;
fnPriceArray['ABCD_20'] = 1049;
fnPriceArray['ABCD_50'] = 2419;
fnPriceArray['ABCD_80'] = 3579;
fnPriceArray['ABCD_100'] = 3958;
fnPriceArray['ABCD_200'] = 6109;
fnPriceArray['ABCD_300'] = 7648;
fnPriceArray['ABCD_400'] = 9048;
fnPriceArray['ABCD_500'] = 10493;
fnPriceArray['ABCD_600'] = 11603;
fnPriceArray['ABCD_700'] = 12547;
fnPriceArray['ABCD_800'] = 13438;
fnPriceArray['ABCD_900'] = 14237;
fnPriceArray['ABCD_1000'] = 15298;
fnPriceArray['ABCD_2000'] = 21947;
fnPriceArray['ABCDG_10'] = 746;
fnPriceArray['ABCDG_20'] = 1262;
fnPriceArray['ABCDG_50'] = 2938;
fnPriceArray['ABCDG_80'] = 4386;
fnPriceArray['ABCDG_100'] = 4880;
fnPriceArray['ABCDG_200'] = 7568;
fnPriceArray['ABCDG_300'] = 9608;
fnPriceArray['ABCDG_400'] = 11469;
fnPriceArray['ABCDG_500'] = 13398;
fnPriceArray['A_10'] = 170;
fnPriceArray['A_20'] = 309;
fnPriceArray['A_50'] = 699;
fnPriceArray['A_80'] = 999;
fnPriceArray['A_100'] = 1099;
fnPriceArray['A_200'] = 1639;
fnPriceArray['A_300'] = 1929;
fnPriceArray['A_400'] = 2199;
fnPriceArray['A_500'] = 2529;
fnPriceArray['A_600'] = 2709;
fnPriceArray['A_700'] = 2919;
fnPriceArray['A_800'] = 3030;
fnPriceArray['A_900'] = 3159;
fnPriceArray['A_1000'] = 3349;
fnPriceArray['A_2000'] = 4029;
fnPriceArray['AC_10'] = 200;
fnPriceArray['AC_20'] = 359;
fnPriceArray['AC_50'] = 819;
fnPriceArray['AC_80'] = 1179;
fnPriceArray['AC_100'] = 1299;
fnPriceArray['AC_200'] = 1959;
fnPriceArray['AC_300'] = 2319;
fnPriceArray['AC_400'] = 2649;
fnPriceArray['AC_500'] = 3034;
fnPriceArray['AC_600'] = 3244;
fnPriceArray['AC_700'] = 3479;
fnPriceArray['AC_800'] = 3610;
fnPriceArray['AC_900'] = 3759;
fnPriceArray['AC_1000'] = 3999;
fnPriceArray['AC_2000'] = 4899;
fnPriceArray['ABCDGI_10'] = 982;
fnPriceArray['ABCDGI_20'] = 1661;
fnPriceArray['ABCDGI_50'] = 3876;
fnPriceArray['ABCDGI_80'] = 5773;
fnPriceArray['ABCDGI_100'] = 6423;
fnPriceArray['ABCDGI_200'] = 9961;
fnPriceArray['ABCDGI_300'] = 12646;
fnPriceArray['ABCDGI_400'] = 15096;
fnPriceArray['ABCDGI_500'] = 17635;
fnPriceArray['ABCDGI_600'] = 19644;
fnPriceArray['ABCDGI_700'] = 21303;
fnPriceArray['ABCDGI_800'] = 22916;
fnPriceArray['ABCDGI_900'] = 24310;
fnPriceArray['ABCDGI_1000'] = 26208;
fnPriceArray['ABCDGI_2000'] = 38791;
fnPriceArray['ABCDGFI_10'] = 1089;
fnPriceArray['ABCDGFI_20'] = 1842;
fnPriceArray['ABCDGFI_50'] = 4288;
fnPriceArray['ABCDGFI_80'] = 6401;
fnPriceArray['ABCDGFI_100'] = 7122;
fnPriceArray['ABCDGFI_200'] = 11045;
fnPriceArray['ABCDGFI_300'] = 14022;
fnPriceArray['ABCDGFI_400'] = 16738;
fnPriceArray['ABCDGFI_500'] = 19533;
fnPriceArray['ABCDGFI_600'] = 21780;
fnPriceArray['ABCDGFI_700'] = 23620;
fnPriceArray['ABCDGFI_800'] = 25408;
fnPriceArray['ABCDGFI_900'] = 26953;
fnPriceArray['ABCDGFI_1000'] = 29085;
fnPriceArray['ABCDGFI_2000'] = 43009;

fnPriceArray['U_10'] = 119;
fnPriceArray['U_20'] = 180;
fnPriceArray['U_50'] = 450;
fnPriceArray['U_80'] = 700;
fnPriceArray['U_100'] = 800;
fnPriceArray['U_200'] = 1400;
fnPriceArray['U_300'] = 1900;
fnPriceArray['U_400'] = 2300;
fnPriceArray['U_500'] = 2700;
fnPriceArray['U_1000'] = 3500;
fnPriceArray['U_2000'] = 4800;

fnPriceArray['AU_10'] = 620;
fnPriceArray['AU_20'] = 1060;
fnPriceArray['AU_50'] = 2450;
fnPriceArray['AU_80'] = 3600;
fnPriceArray['AU_100'] = 4000;
fnPriceArray['AU_200'] = 7000;
fnPriceArray['AU_500'] = 17000;
fnPriceArray['AU_2000'] = 38000;

var fnNumLinkArray = new Array();
fnNumLinkArray['AD_20'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100878';
fnNumLinkArray['AD_50'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100879';
fnNumLinkArray['AD_80'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100880';
fnNumLinkArray['AD_100'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100881';
fnNumLinkArray['AD_200'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100882';
fnNumLinkArray['AD_500'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100885';

fnNumLinkArray['ACD_20'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100444';
fnNumLinkArray['ACD_50'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100445';
fnNumLinkArray['ACD_80'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100446';
fnNumLinkArray['ACD_100'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100447';
fnNumLinkArray['ACD_200'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100448';
fnNumLinkArray['ACD_500'] = '';

fnNumLinkArray['ABD_20'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100620';
fnNumLinkArray['ABD_50'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100621';
fnNumLinkArray['ABD_80'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100622';
fnNumLinkArray['ABD_100'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100623';
fnNumLinkArray['ABD_200'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100624';
fnNumLinkArray['ABD_500'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100625';

fnNumLinkArray['ABC_20'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100869';
fnNumLinkArray['ABC_50'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100870';
fnNumLinkArray['ABC_80'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100871';
fnNumLinkArray['ABC_100'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100872';
fnNumLinkArray['ABC_200'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100873';
fnNumLinkArray['ABC_500'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100874';

fnNumLinkArray['ABCD_20'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100626';
fnNumLinkArray['ABCD_50'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100627';
fnNumLinkArray['ABCD_80'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100628';
fnNumLinkArray['ABCD_100'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100629';
fnNumLinkArray['ABCD_200'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100630';
fnNumLinkArray['ABCD_500'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100631';

fnNumLinkArray['A_20'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100864';
fnNumLinkArray['A_50'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100875';
fnNumLinkArray['A_80'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100865';
fnNumLinkArray['A_100'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100876';
fnNumLinkArray['A_200'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100877';
fnNumLinkArray['A_500'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100866';

fnNumLinkArray['AC_20'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100438';
fnNumLinkArray['AC_50'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100439';
fnNumLinkArray['AC_80'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100440';
fnNumLinkArray['AC_100'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100441';
fnNumLinkArray['AC_200'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100442';
fnNumLinkArray['AC_500'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100443';

fnNumLinkArray['U_20'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100613';
fnNumLinkArray['U_50'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100614';
fnNumLinkArray['U_80'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100615';
fnNumLinkArray['U_100'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100616';
fnNumLinkArray['U_200'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100617';
fnNumLinkArray['U_500'] = 'http://hicloudmall.hinet.net/app_mart/controller?action=amp_product&Apps_PK=100618';

var isUserEmptyMsg = false;
var isAccEmptyMsg = false;
$j(function() {
	$j('#users').bind({
		'focus': function() {
			isUserEmptyMsg = false;
			if($j('#users').val() == '公司人數和上線人數均要填寫') {
				$j('#users').val('');
			}
			$j('#users').css({
				'color': '#000000'
			});
		},
		'blur': function() {
			isUserEmptyMsg = true;
			recommend();
		}
	});
	
	$j('#accounts').bind({
		'focus': function() {
			isAccEmptyMsg = false;
			if($j('#accounts').val() == '公司人數和上線人數均要填寫') {
				$j('#accounts').val('')
			}
			$j('#accounts').css({
				'color': '#000000'
			});
		},
		'blur': function() {
			isAccEmptyMsg = true;
			recommend();
		}
	});
});

function recommend() {
  var pass1 = true;
  var pass2 = true;
  
  $j('#account_err').hide();
  
  var func = $j('#packages').val();

  var users = $j('#users').val();
  var accounts = $j('#accounts').val();

  hideAll();

  if (accounts != '' && users == '') {
    if(isUserEmptyMsg) {
      $j('#users').val('公司人數和上線人數均要填寫');
      $j('#users').css('color', 'red');
    }    
    return false;
  }else if(accounts == '' && users != ''){
    if(isAccEmptyMsg) {
      $j('#accounts').val('公司人數和上線人數均要填寫');
      $j('#accounts').css('color', 'red');
    }
  }else if(parseInt(accounts) > parseInt(users)){
    $j('#account_err').show();
    $j('#account_err').html('上線人數不應多於公司人數');
    return false;
  }

  var accnum1 = '';
  var accnum2 = '';
  var usernum1 = 0;
  var usernum2 = '';
  var aunum = 0;
  var usernum3 = 0;
  var addAU = false;
  var tmpPrice1 = 0;
  var tmpPrice2 = 0;
  var tmpPrice3 = 0;
  var totalPrice = 0;


  $j.each(userNumArray, function(acckey, accval) {
    if (accounts <= parseInt(accval)) {
      if (acckey > 0) {
        accnum2 = userNumArray[parseInt(acckey) - 1];
        $j.each(userNumArray, function(acckey2, accval2) {
          if((parseInt(accnum2) + parseInt(accval2) >= parseInt(accounts)) && (parseInt(accval) != accounts)){
            aunum = accval2;
            return false;
          }
        });
      }
      accnum1 = accval;      
      return false;
    }
  });

  if (accnum1 != '') {
    var usernum1b = parseInt(users) - parseInt(accnum1);
    if (usernum1b > 0) {
      usernum1 = findUserNum(usernum1b);  // expand user count
      if (usernum1 == '') {
        pass1 = false;
      }
    }
  } else {
    pass1 = false;
  }

  if (accnum2 != '') {
    var usernum2b = parseInt(users) - parseInt(accnum2);
    if (usernum2b > 0) {
      usernum2 = findUserNum(usernum2b);
      if (usernum2 == '') {
        pass2 = false;
      }
    }
  } else {  
    pass2 = false;
    /*here we check if upper plan price is lower than recommd plan1 or not*/
		var usernum4 = 0;
		var accnum4 = userNumArray[1];    
		var usernum4b = parseInt(users) - parseInt(accnum4);
    if (usernum4b > 0) {
      usernum4 = findUserNum(usernum4b); 
    }
  }

  if(aunum != 0){
    var usernum3b = parseInt(users) - parseInt(accnum2) - parseInt(aunum);
    if(usernum3b > 0){
      usernum3 = findUNum(usernum3b);  // expand user count
    }
    var packcode = $j('#packages').val();
    var price1 = fnPriceArray[packcode + '_' + accnum2] + fnPriceArray['AU_' + aunum];
    if(usernum3 > 0){
      price1 += fnPriceArray['U_' + usernum3];
    }
    var price2 = fnPriceArray[packcode + '_' + accnum1];    
    if(usernum1 > 0){
      usernum1 = findUNum(usernum1);
      price2 += fnPriceArray['U_' + usernum1];
    }
    if(price1 < price2){
      addAU = true;
      accnum1 = accnum2;
      usernum1 = usernum3;
      var recommendPrice1 = price1;
    }else{
      aunum = 0;
      var recommendPrice1 = price2;
		}
  }else{
    var packcode = $j('#packages').val();
		var recommendPrice1 = fnPriceArray[packcode + '_' + accnum1];
    if(usernum1 > 0){
      usernum1 = findUNum(usernum1);
      recommendPrice1 += fnPriceArray['U_' + usernum1];
    }
	}

	if(pass2 == false){
		if(accnum4 > 0){
    	var packcode = $j('#packages').val();
			var price4 = fnPriceArray[packcode + '_' + accnum4];    
			if(usernum4 > 0){
        usernum4 = findUNum(usernum4);
        price4 += fnPriceArray['U_' + usernum4];
    	}

			if(price4 < recommendPrice1){
      	accnum1 = accnum4;
      	usernum1 = usernum4;
    	}
		}
	}

  if (pass1) {
    var packcode1 = $j('#packages').val();
    var packname1 = fnNameArray[packcode1];
    var totuser1 = parseInt(accnum1) + parseInt(usernum1);
    var totacc1 = parseInt(accnum1);
    if(addAU == true){
      totuser1 += parseInt(aunum);
      totacc1 += parseInt(aunum);
    }
    $j('#p1').show();
    $j('#p1c').show();
    $j('#ac1').html(totacc1);
    $j('#u1').html(totuser1);
    $j('#accountNums1').val(totacc1);
    $j('#userNums1').val(totuser1);
    $j('#auNums1').val(aunum);    
    $j('#func1').html(packname1);
   
    tmpPrice1 = fnPriceArray[packcode1 + '_' + accnum1];
    if (usernum1 != '') {
      tmpPrice2 = fnPriceArray['U_' + usernum1];
    }
    if(addAU == true){
      tmpPrice3 = fnPriceArray['AU_' + aunum];
    }
    totalPrice = tmpPrice1 + tmpPrice2 + tmpPrice3;

    var sol1p1 = '<tr><td height="20px">' + packname1 + accnum1 + '人</td><td align="right" width="30%" valign="top">' + tmpPrice1 + '元</td></tr>';
    var sol1p2 = '<tr><td height="20px">擴充員工數' + usernum1 + '人</td><td align="right">' + tmpPrice2 + '元</td></tr>';
    var sol1p3 = '<tr><td height="20px">擴充帳號員工數' + aunum + '人</td><td align="right">'+ tmpPrice3 + '元</td></tr>';
    
    var sol1Html = '<table style="min-width:75%"><tr><td colspan="2">月租費：</td></tr>';
    sol1Html += sol1p1;
    sol1Html += (usernum1 != '') ? sol1p2 : '';
    sol1Html += (addAU == true) ? sol1p3 : '';
    sol1Html += '<tr><td colspan="2">共 '+ totalPrice +'元</td></table>';
    $j('#sol1').html(sol1Html);

    //$j('#each1').html(prctotal + ' / ' + totuser1  + '人 / ' + 30 + '天 = ' + Math.round(prctotal / totuser1 / 30 * 100) / 100);
    var buyHtml1 = '<INPUT type="radio" name="data[NewOrder][check]" id="check1" value="1" checked> ';
    $j('#buy1').html(buyHtml1);
  }

  if (pass2) {
    $j('#has2').show();
    var packcode2 = $j('#packages').val();
    var packname2 = fnNameArray[packcode2];
    var totuser2 = parseInt(accnum2) + parseInt(usernum2);
    $j('#p2').show();
    $j('#p2c').show();
    $j('#ac2').html(accnum2);
    $j('#u2').html(totuser2);
    $j('#accountNums2').val(accnum2);
    $j('#userNums2').val(totuser2);
    $j('#func2').html(packname2);

    tmpPrice1 = 0;
    tmpPrice2 = 0;
    totalPrice = 0;
    
    tmpPrice1 = fnPriceArray[packcode2 + '_' + accnum2];    
    if (usernum2 != '') {
      usernum2 = findUNum(usernum2);
      tmpPrice2 = fnPriceArray['U_' + usernum2];
    }
    totalPrice = tmpPrice1 + tmpPrice2 ;
   
    var sol2p1 = '<tr><td height="20px">' + packname2 + accnum2 + '人</td><td align="right" width="30%" valign="top">' + tmpPrice1 + '元</td></tr>';
    var sol2p2 = '<tr><td height="20px">擴充員工數' + usernum2 + '人</td><td align="right">' + tmpPrice2 + '元</td></tr>';

    var sol2Html = '<table style="min-width:70%"><tr><td colspan="2">月租費：</td></tr>';
    sol2Html += sol2p1;
    sol2Html += (usernum2 != '') ? sol2p2 : '';
    sol2Html += '<tr><td colspan="2">共 '+ totalPrice +'元</td></table>';
    $j('#sol2').html(sol2Html);

    /*$j('#each2').html(Math.round(prctotal / totuser2 / 30 * 100) / 100 + ' (' + prctotal + '/' + totuser2 + ')');*/
    /*$j('#each2').html(prctotal + ' / ' + totuser2  + ' / ' + 30 + ' = ' + Math.round(prctotal / totuser2 / 30 * 100) / 100);*/
    var buyHtml2 = '<INPUT type="radio" name="data[NewOrder][check]" id="check2" value="2">';
    $j('#buy2').html(buyHtml2);
  }

  if(!pass2 && pass1){
    $j('#rec2').css('display', 'none');
    $j('#rec1').css('width', '98%');
    $j('#p1_status').hide();
    $j('#pr1c').show();
  }else if(pass2 && pass1){
    $j('#rec2').css('display', '');
    $j('#rec1').css('width', '47%');
    $j('#p1_status').hide();
    $j('#pr1c').show();
  }
  if (!pass1 && !pass2 && parseInt(accounts) && parseInt(users)) {
    //$j('#p4').show();
    $j('#rec2').css('display', 'none');
    $j('#rec1').css('width', '98%');
    $j('#pr1c').hide();
    $j('#p1_status').show();    
    $j('#p1_status').html('無適合套裝方案，請與我們聯繫！');
    $j('#err_msg').val('無適合套裝方案，請與我們聯繫！');
  }else{
    $j('#err_msg').val('');
  }
}


function hideAll() {
    $j('#p1').hide();
    $j('#p1c').hide();
    $j('#p2').hide();
    $j('#p2c').hide();
    $j('#p3').hide();
    $j('#p4').hide();
    $j('#alert').hide();
    $j('#has2').hide();
    $j('#packs1').html('');
    $j('#packs2').html('');
}

function findUserNum(given) {
  var unum = '';
  $j.each(userNumArray, function(ukey, uval) {
    if (given <= parseInt(uval)) {
      unum = uval;
      return false;
    }
  });
  return unum;
}

function findUNum(given) {
  var unum = '';
  $j.each(uNumArray, function(ukey, uval) {
    if (given <= parseInt(uval)) {
      unum = uval;
      return false;
    }
  });
  return unum;
}

function goStep2(){
  if( $j("#name").val() == '' || $j("#name").val() == '公司名稱(用來產生系統名稱)'){
    alert('公司名稱為必填'); $j("#name").css("color","red");
    return false;  
  }else if($j("#contact").val() == '' || $j("#contact").val() == '連絡人'){
    alert('連絡人為必填'); $j("#contact").css("color","red");
    return false;
  }else if($j("#email").val() == '' || $j("#email").val() == '連絡信箱(用來寄送帳號與密碼)'){
    alert('連絡信箱為必填'); $j("#email").css("color","red");
    return false;
  }else if($j("#phone").val() == '' || $j("#phone").val() == '連絡電話(帳戶問題聯絡用)'){
    alert('連絡電話為必填'); $j("#phone").css("color","red");
    return false;
  }else if(!validate($j("#email").val())){
    alert('連絡信箱 格式錯誤'); $j("#email").css("color","red");
    return false;
  }else{
    $j("#step2").show();
    $j("#step1").hide();
  }
}

function startTrial(){
  var regExp =/^(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/;
	if( $j("#name").val() == '' || $j("#name").val() == '公司名稱(用來產生系統名稱)' || $j("#name").val() == '' || $j("#name").val() == '　'){
    alert('公司名稱為必填'); $j("#name").css("color","red");
    return false;  
  }else if($j("#contact").val() == '' || $j("#contact").val() == '連絡人'|| $j("#contact").val() == '' || $j("#contact").val() == '　'){
    alert('連絡人為必填'); $j("#contact").css("color","red");
    return false;
  }else if($j("#title").val() == '' || $j("#title").val() == '職稱'|| $j("#title").val() == '' || $j("#title").val() == '　'){
    alert('職稱為必填'); $j("#title").css("color","red");
    return false;
  }else if($j("#email").val() == '' || $j("#email").val() == '連絡信箱(用來寄送帳號與密碼)'|| $j("#email").val() == '' || $j("#email").val() == '　'){
    alert('連絡信箱為必填'); $j("#email").css("color","red");
    return false;
  }else if(!validate($j("#email").val())){
    alert('連絡信箱格式錯誤'); $j("#email").css("color","red");
    return false;
  }else if($j("#phone").val() == '' || $j("#phone").val() == '連絡電話(帳戶問題聯絡用)'|| $j("#phone").val() == '' || $j("#phone").val() == '　'){
    alert('連絡電話為必填'); $j("#phone").css("color","red");
    return false;
  }else if($j("#username").val() == '' || $j("#username").val() == '系統預設帳號'|| $j("#username").val() == '' || $j("#username").val() == '　'){
    alert('系統預設帳號為必填'); $j("#username").css("color","red");
    return false;
  }else if( $j("#q1").val() == '' || $j("#q1").val() == '您是如何得知Femas HR系統？'|| $j("#q1").val() == '' || $j("#q1").val() == '　'){
    alert('您是如何得知Femas HR系統為必填'); $j("#q1").css("color","red");
    return false;  
  }else if($j("#users").val() == '' || $j("#users").val() == '公司人數和上線人數均要填寫'|| $j("#users").val() == '' || $j("#users").val() == '　'){
    alert('貴公司總人數為必填'); $j("#users").css("color","red");
    return false;
  }else if($j("#accounts").val() == '' || $j("#accounts").val() == '公司人數和上線人數均要填寫'|| $j("#accounts").val() == '' || $j("#accounts").val() == '　'){
    alert('上線的帳號數為必填'); $j("#accounts").css("color","red");
    return false;
  }else if(parseInt($j('#users').val()) < parseInt($j('#accounts').val())){
    alert('上線人數不應多於公司人數');
    return false;
  }else if(in_array($j("#sn").val(), syspath)){
    alert('此路徑已被系統使用，請選擇其他名稱'); $j("#sn").css("color","red");
    return false;
  }else if($j("#sn").val().length < 3 || !regExp.test($j("#sn").val())){
    alert('專屬路徑只能由英文及數字組成，\n最少需含一個英文字母且長度須為3碼(含)以上');$j("#sn").css("color","red");
    return false;
	}else if($j("#username").val().toLowerCase() == 'admin'){
    alert('無法使用此帳號做為預設帳號，請選擇其他名稱'); $j("#username").css("color","red");
    return false;
  }else if($j("#err_msg").val()){
    alert($j("#err_msg").val());
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
  //var regExp = /^[\d|a-zA-Z]+$/;
  var regExp =/^(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/;
  if(str.length < 3){
    alert('專屬路徑只能由英文及數字組成，\n最少需含一個英文字母且長度須為3碼(含)以上');
    return str;
	}else{
		if (regExp.test(str)){
    	str = str.toLowerCase();
    	return str;
  	}else if(str == '您的專屬路徑(限英文及數字)'){
    	return str;
  	}else{
    	alert('專屬路徑只能由英文及數字組成，\n最少需含一個英文字母且長度須為3碼(含)以上');
    	return str;
  	}
  }
}

function checkValAcc( str ) {
  var regExp = /^[\d|a-zA-Z]+$/;  
	if (regExp.test(str)){
    str = str.toLowerCase();
    return str;
  }else if(str == '系統預設帳號'){
    return str;
  }else{
    alert('只能由英文及數字組成');
    return '';
  }
}


function showNote(){
  $j("#note").show();
  $j("#noteSw2").show();
  $j("#noteSw1").hide();
  $j("#step1_right").css("height","820px")
}

function closeNote(){
  $j("#note").hide();
  $j("#noteSw2").hide();
  $j("#noteSw1").show();
  $j("#step1_right").css("height","700px")
}

function in_array(s, a) {
  for (k=0; v=a[k]; k++) {
    if (v==s) {
      return true;
    }
  }
  return false;
}