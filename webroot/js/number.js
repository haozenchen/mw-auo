function checkNumber(checknum){
	num = checknum.replace(/[,]+/g, "");
	var check = num.match(/^-?\d+\.?\d*?$/);
	if(!check){
		alert('請輸入半形數字0~9');
			checknum = '';
	}
	return checknum;
}




