<?php
  $vars = $this->viewVars;
  if (isset($vars['type']) && ($vars['type']=='pass')){
		$class = 'passMsg';
	} else {
		$class = 'errMsg';
	}
?>
<script language="javascript">
	Element.update('flashMessage', "<div class=<?echo $class?>><?php echo $content_for_layout; ?></div>");
	Element.show('flashMessage');
</script>
