function NumberToChinese(money, defaultNum)
{
	var chWord = new Array('零','壹','貳','參','肆','伍','陸','柒','捌','玖');
	var numWord = new Array('元整', '拾', '佰', '仟', '萬', '拾', '佰', '仟', '億', '拾', '佰', '仟', '兆', '拾', '佰', '仟');
	var moneyStr = String(money);
	var moneyReturn = '';
	var n = 0;
	if(isFinite(money)==false)
 	{
   	return;
 	}
	moneyLeng = moneyStr.length;
 	if (moneyLeng == 0) return;
	if (moneyLeng > 16) return;
	for (i=(moneyLeng - 1); i>=0; i=i-1) {
	   moneyReturn = ' '+chWord[moneyStr.substr(i, 1)]+numWord[n]+moneyReturn;
	   n = n + 1;
	}
	if (defaultNum != 0 && moneyLeng < defaultNum) {
		for (i = moneyLeng; i < defaultNum; i=i+1) {
			moneyReturn = ' '+chWord[0]+numWord[n]+moneyReturn;
	   	n = n + 1;
		}
	}
	return moneyReturn;
}