<?php
  $vars = $this->viewVars;
  if (isset($vars['type']) && ($vars['type']=='pass')){
		$class = 'passMsg';
	} else {
		$class = 'errMsg';
	}
?>
<script language="javascript">
	Element.update('flashFreeMessage', "<font class=<?echo $class?>><?php echo $content_for_layout; ?></font>");
	Element.show('flashFreeMessage');
</script>
