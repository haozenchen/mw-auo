<!DOCTYPE html>
<html>
<head>
<title>鋒形科技人力資源系統免費試用申請</title>
<meta http-equiv="description" content="鋒形科技人力資源系統">
<meta http-equiv="keywords" content="差勤,差勤管理,差勤表單,電子表單,人力資源,電子公佈欄,排班,護理排班,排班管理,護理資訊,排班系統,公文管理,文件管理">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
<?php echo $html->charset();?>
<?php $javascript->link('prototype') ?>

<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>

<?php
	//ad script
	for($i=0; $i<4; $i++){
		if(!empty(${'adScript'.ife($i>0, $i, '')})) {
			echo ${'adScript'.ife($i>0, $i, '')};
		}
	}
?>
</head>
<body>
<?php
	//ad script body
	for($i=0; $i<4; $i++){
		if(!empty(${'adScriptBody'.ife($i>0, $i, '')})) {
			echo ${'adScriptBody'.ife($i>0, $i, '')};
		}
	}
?>
<?php echo $content_for_layout;?>
<?php
	if($session->check('Message.flash')):
		$session->flash();
	endif;
?>
<div id="transbtm" style="display: none;"></div>
</body>
</html>
