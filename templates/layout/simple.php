<html>
<head>
<?php echo $this->Html->meta('charset', 'UTF-8'); ?>

</head>
<script type="text/javascript">
	function ftarget(){
		if(jQuery('#w2ui-popup').length){
			return '#w2ui-popup';
		}else{
			return 'body';
		}
	}
</script>
<body>
<?= $this->fetch('content') ?>
</body>
</html>