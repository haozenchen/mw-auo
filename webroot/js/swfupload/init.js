var swfu;

document.emmaUploadInit = function () {
	swfu = new SWFUpload({
		/**
		 * we get parameters from controller, such as beforeRender
		 */
		upload_url	: upscr,
		flash_url	: flpth,
		file_size_limit	: afsz,
		file_types	: '*.*',
		custom_settings	: {
			progressTarget	:	'fsUploadProgress',
			cancelButtonId	:	'btnCancel'
		},
		button_placeholder_id	: 'SWFUploadButtonPlaceHolder',
		button_text	: '<span class="theFont">選擇檔案</span>',
		button_text_style	: ".theFont { font-size: 12px; }",
		button_image_url: "../../img/select_file.png",	// Relative to the Flash file
		button_width	: '72',
		button_height	: '22',
		button_text_left_padding	: '12',
		button_text_top_padding		: '3',
		//for overlay button_window_mode		: SWFUpload.WINDOW_MODE.TRANSPARENT,
		//for overlay button_cursor			: SWFUpload.CURSOR.HAND,
		file_queued_handler		: fileQueued,
		file_queue_error_handler	: fileQueueError,
		file_dialog_complete_handler	: fileDialogComplete,
		upload_start_handler		: uploadStart,
		upload_progress_handler		: uploadProgress,
		upload_error_handler		: uploadError,
		upload_success_handler		: uploadSuccess,
		upload_complete_handler		: uploadComplete,
		queue_complete_handler		: queueComplete,

		dummy				: 'dummy'	// for last line without ',', add no param after this
	});
};
if (window.inAjaxReq) {
	document.emmaUploadInit();
} else {
	window.onload = document.emmaUploadInit;
}
