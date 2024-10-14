<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo $sysName ?></title>

<meta http-equiv="description" content="鋒形科技差勤表單系統 [FONSEN iHRM System]">
<meta http-equiv="keywords" content="">

<?php echo $html->charset();?>
<?php echo $html->css('emma.default');?>
<?php echo $javascript->link('prototype') ?>
<?php echo $javascript->link('printing') ?>

<style type="text/css">
<!--
body {
	margin-left:0;
	margin-top:0;
	margin-right:0;
	margin-bottom:0;
	font-size:11pt;
	font-family:標楷體;
}

.prControl {
	text-align:right;
	background-color:#FFFFCC;
	border-bottom:1pt solid #FF9900;
}

.prControl .printDesc {
	padding-right:100pt;
	color:#990000;
}

@media print {
	.prControl {
		display:none;
	}
}

thead {
	display:table-header-group;
}

tfoot {
	display:table-footer-group;
	vertical-align: middle;
	height: 60px;
}

.prFooter {
	height:60px;
}
-->
</style>

</head>

<?php if (@$noprint === true) : ?>
<body>
<?php else : ?>
<body onload="window.print()">
<?php endif; ?>

<?php
	$printSize = @$printFormat[0];
	$printDirect = @$printFormat[1];
	$directStyle = array('O'=>'直', 'L'=>'橫');
	$printDesc = ife($printSize and $printDirect, '建議採【' . $printSize . '】格式，並以【' . @$directStyle[$printDirect] . '式】列印。', '');
?>

<div class="prControl">
	<span class="printDesc"><?php echo $printDesc ?></span>
	<?php if (!empty($layoutPageBreak)) : // provide function to change page break on the fly ?>
	<button onclick="setPageBreak($('inpPageBreak').value); return false;">設定</button>換頁行數<input name="inpPageBreak" id="inpPageBreak" value="<?php echo $layoutPageBreak; ?>" size="1">
	<?php endif; ?>
	<button onclick="window.print(); return false;" style="cursor: pointer;">列印</button>
</div>

<div style="position: relative;">
	<?php if (@$layoutNoPrnDate !== true) : ?>
	<div class="textBlack" style="position:absolute; top:0; right:0;"><?php echo __('列印日期：', true) . date(EMMA_TIME_FORM); ?></div>
	<?php endif; ?>
	<div><?php echo $content_for_layout;?></div>
</div>

<script>
	<?php if (!empty($layoutPageBreak)) : ?>
	setPageBreak(<?php echo $layoutPageBreak; ?>);
	<?php endif; ?>
</script>

</body>
</html>