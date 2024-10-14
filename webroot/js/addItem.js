function optionsList(type) {
	if(type == 2 || type == 3) {
		Element.show(selectNext);
		Element.show(item);
		Element.hide(text);
		Element.hide(textArea);
	} else if (type == 1) {
		Element.show(text);
		Element.hide(textArea);
		Element.hide(selectNext);
		Element.hide(item);
	} else {
		Element.hide(text);
		Element.hide(selectNext);
		Element.hide(item);
	}
}

function defaultItem(selectValue) {
	count = document.getElementById(amount).value;
	var beforeItem = "";
	for (i=1; i<=count; i++) {
		beforeValue = selectValue[i];
		beforeItem = beforeItem + i + ". <input type='text' id="+head+i+" name='data[ExamTopic][options"+i+"]' size='26' class='textBlack' value='"+beforeValue+"'>&nbsp;<a href='#' onclick='deleteItem("+i+")'>x</a><br>";
	}
	document.getElementById(item).innerHTML = beforeItem;
}

function addItemValue(count){
	var selectValue = new Array(10);
	for (i=1; i<count; i++) {
		selectValue[i] = document.getElementById(head+i).value;
	}
	return selectValue;
}

function addItem(){
	document.getElementById(amount).value = (document.getElementById(amount).value - 0) + 1;
	count = document.getElementById(amount).value;
	itemValue = count;
	selectValue = addItemValue(count);
	var beforeItem = "";
	for (i=1; i<count; i++) {
		beforeValue = selectValue[i];
		beforeItem = beforeItem + i + ". <input type='text' id="+head+i+" name='data[ExamTopic][options"+i+"]' size='26' class='textBlack' value='"+beforeValue+"'>&nbsp;<a href='#' onclick='deleteItem("+i+")'>x</a><br>";
	}
	document.getElementById(item).innerHTML = beforeItem + itemValue + ". <input type='text' id="+head+count+" name='data[ExamTopic][options"+count+"]' size='26' class='textBlack'><br>";
	document.getElementById(head+count).select();
}

function deleteItemValue(count, delItem) {
	var selectValue = new Array(10);
	var reset = 1;
	for (i=1; i<=count; i++) {
		if(delItem != i) {
			selectValue[reset] = document.getElementById(head+i).value;
			reset += 1;
		}
	}
	document.getElementById(amount).value = (document.getElementById(amount).value - 0)- 1;
	return selectValue;
}

function deleteItem(delItem){
	document.getElementById(amount).value = (document.getElementById(amount).value - 0);
	count = document.getElementById(amount).value;
	selectValue = deleteItemValue(count, delItem);
	var beforeItem = "";
	for (i=1; i<count; i++) {
		beforeValue = selectValue[i];
		beforeItem = beforeItem + i + ". <input type='text' id="+head+i+" name='data[ExamTopic][options"+i+"]' size='26' class='textBlack' value='"+beforeValue+"'>&nbsp;<a href='#' onclick='deleteItem("+i+")'>x</a><br>";
	}
	//beforeItem = beforeItem + i + ". <input type='text' id="+head+i+" name='data[ExamTopic][options"+i+"]' size='26' class='textBlack' value='"+beforeValue+"'><br>";
	document.getElementById(item).innerHTML = beforeItem;
}

function disabledButton(countTopic) {
	var check = false;
	for(var i =0; i<=countTopic;i++){
		if(document.getElementById(i).checked){
			check = false;	
		}
	}
	if(check == false) {
		document.getElementById('saveButton').disabled  = true;
	} else {
		document.getElementById('saveButton').disabled  = false;
	}
}
