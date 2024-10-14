/**
 * callback before upload
 */
function beforeUpload(){
	Element.show('upload_status');
	Element.update('upload_status', '上傳中 ...');
	return true;
}

/**
 * callback after upload
 */
function afterUpload(mesg){
	Element.update('upload_status', mesg);
	return true;
}