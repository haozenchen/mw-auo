<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo $sysName ?></title>

<meta http-equiv="description" content="">
<meta http-equiv="keywords" content="<?php echo $sysName ?>">

<?php echo $html->charset();?>
<?php echo $html->css('emma.default');?>
<?php echo $javascript->link('prototype') ?>

</head>
<body>

<div><?php echo $content_for_layout;?></div>

</body>
</html>
