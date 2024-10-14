<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo $sysName ?></title>
<meta http-equiv="description" content="鋒形科技差勤表單系統 [FONSEN iHRM System]">
<meta http-equiv="keywords" content="人力資源,薪資管理,勞健保管理,勞退管理,人事管理,差勤管理,電子表單,電子公佈欄,排班,公文管理,文件管理">
<meta http-equiv="pragma" content="no-cache"> 
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
<?php echo $html->charset();?>
<?php echo $html->css('emma.default');?>
<?php echo $javascript->link('prototype') ?>
<?php echo $javascript->link('scriptaculous') ?>
<?php echo $javascript->link('cookie') ?>
<?php echo $javascript->link('hiddendiv') ?>

<?php
	//ad script
	if(!empty($adScript)) {
		echo $adScript;
	}
?>
</head>
<body>
	<table class="std" cellpadding="10" width="100%">
		<tr>
			<td valign="top" align='left'>
				<span id="flashMessage"></span>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo $content_for_layout;?>
			</td>
		</tr>
	</table>
</body>
</html>
