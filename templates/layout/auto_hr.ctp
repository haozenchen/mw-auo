<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo $sysName ?></title>
<meta http-equiv="description" content="<?php echo __('鋒形科技人力資源系統', true)?>">
<meta http-equiv="keywords" content="<?php echo __('差勤,差勤管理,差勤表單,電子表單,人力資源,電子公佈欄,排班,護理排班,排班管理,護理資訊,排班系統,公文管理,文件管理', true)?>">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
<?php echo $html->charset();?>
<?php echo $html->css('emma.default');?>
<?php echo $javascript->link('prototype') ?>

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

</head>
<body>

<?php echo $content_for_layout;?>
<?php
	if($session->check('Message.flash')):
		$session->flash();
	endif;
?>
</body>
</html>
