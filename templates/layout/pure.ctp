<html>
<head>
<?php echo $html->charset() ?>
<?php
	//ad script
	if(!empty($adScript)) {
		echo $adScript;
	}
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php echo $html->css('pure-min.0.6.0');?>
<?php echo $html->css('grids-responsive-min');?>
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
<?php echo $content_for_layout ?>
</body>
</html>