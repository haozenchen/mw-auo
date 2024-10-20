<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

<meta name="viewport" content="width=device-width, initial-scale=1 ,maximum-scale=1.0, user-scalable=0">
<meta http-equiv="description" content="鋒形科技人力資源管理系統">
<meta http-equiv="keywords" content="人力資源管理,差勤,表單,薪資,勞健保,教育訓練,排班">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
<?php echo $this->Html->meta('charset', 'UTF-8'); ?>
<?php echo $this->Html->css('bootstrap') ?>
<?php echo $this->Html->css('login') ?>
<?php echo $this->Html->css('toastr.min') ?>
<?php echo $this->Html->script('jquery-2.1.0') ?>
<?php echo $this->Html->script('prototype') ?>
<?php echo $this->Html->script('bootstrap-typeahead') ?>
<?php echo $this->Html->script('toastr.min') ?>

<style type="text/css">
	body {
		margin-left: 0px;
		margin-top: 0px;
		margin-right: 0px;
		margin-bottom: 0px;
	}
</style>

<?php
	//ad script
	if(!empty($adScript)) {
		echo $adScript;
	}
?>
</head>
<body onload="initial();">


<?= $this->fetch('content') ?>
<?php
	if($this->request->getSession()->check('Message.flash')):
		$this->request->getSession()->flash();
	endif;
?>
</body>
</html>