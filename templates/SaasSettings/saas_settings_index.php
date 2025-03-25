<?php echo $this->App->safeScript('screen') ?>
<?php echo $this->App->safeScript('div') ?>
<?php echo $this->App->safeCss('classical'); ?>
<?php echo $this->App->safeCss('emma.advance') ?>
<?php echo $this->App->safeCss('tag_menus'); ?>

<div style="padding: 10px; margin-left: 10px; text-align: left; font-family: 'Noto Sans TC', sans-serif; font-weight: 900; color: #666">
	<?php echo __('參數設定', true); ?> <span id="navi_title"></span>
</div>
<div id="listing" class="content">
	<?php require('setting_form.php'); ?>
</div>