<?php echo $this->App->safeScript('div') ?>
<?php echo $this->App->safeScript('screen') ?>
<?php echo $this->App->safeCss('classical') ?>
<?php echo $this->App->safeCss('emma.advance') ?>
<style type="text/css">
.w2ui-buttons{
    position: fixed;
    top: 0px;
    right: 130px;
}
</style>
<div style="padding: 10px; margin-left: 10px; text-align: left; font-family: 'Noto Sans TC', sans-serif; font-weight: 900; color: #666">
	<?php echo __('權限群組管理', true); ?> <span id="navi_title"></span>
	<div style="float: right; margin-right: 5px">
		<button class="w2ui-btn" style="display: none;" id="back_btn" name="close" onclick="goBack()"><i class="fas fa-arrow-left"></i> 返回</button>
		<?php echo $this->Form->hidden('reload_list') ?>
	</div>
</div>
<div id="listing" class="content"><?php require('listing.php'); ?></div>
<script>
function goBack(){
	w2ui['layout'].show('top'); 
	w2ui['layout'].hide('main');
	jQuery('#back_btn').hide();
}
</script>