<?php
  if (!isset($ajaxmsgbx_tag)) {
    $ajaxmsgbx_tag = $this->action;
  }
?>
<script language="javascript">
	Element.update("ajaxmsgbx_<?=$ajaxmsgbx_tag?>", "<?php echo $content_for_layout; ?>");
	Element.show('boxError');
</script>
