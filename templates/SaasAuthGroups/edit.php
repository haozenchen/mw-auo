<style>
	div.other{
		padding-left:15%;
	}

	<?php if($advancePermission != 'grant_all') { ?>
		#grant_all{
			display:none;
		}
	<?php } ?>
</style>

<?= $this->Form->create($saasAuthGroup,['id'=>'formEdit']) ?>
	<table class="Classical" align="center" width="100%">
		<tr>
			<td class="title right"><?php echo __('名稱', true); ?></td>
			<td class="content left">
				<?php echo $this->Form->control('name',['label'=>false, 'class'=>'textBlack', 'size'=>'40', 'maxlength'=>'40','autocomplete'=>'off']);  ?>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('首頁', true); ?></td>
			<td class="content left">
				<?php echo $this->Form->control('home',['label'=>false, 'class'=>'textBlack', 'type'=>'select', 'empty' => false, 'options'=>$actionOpts]);  ?>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('描述', true); ?></td>
			<td class="content left">
				<?php echo $this->Form->control('remark',['label'=>false, 'class'=>'textBlack']);  ?>
			</td>
		</tr>
		<tr id="permissions">
			<td class="title right"><?php echo __('權限', true); ?></td>
			<td class="content left">
				<table style="width: 90%" id="permission_checks">
					<tr>
						<td style="width: 30%; vertical-align: top;" rowspan="2"><?php echo __('功能選單', true); ?></td>
						<td style="width: 20%; vertical-align: top;"  rowspan="2">
							<div class="d-flex justify-content-center">
								<input type="checkbox" name="chk_all_act" id="chk_all_act">
								<div class="ml-1"><?php echo __('授權使用', true); ?></div>
							</div>
						</td>
						<td style="text-align: center;" colspan="8"><?php echo __('細部授權', true); ?></td></tr>
					<tr>
						<td style="text-align:center"><?php echo __('新增', true); ?></td>
						<td style="text-align:center"><?php echo __('修改', true); ?></td>
						<td style="text-align:center"><?php echo __('刪除', true); ?></td>
					</tr>
				<?php
					foreach ($menus as $k => $menu) {
						echo '<tr style="border-top:1px solid #ccc"><td>'.$menu['name'].'</td>';
						echo '<td style="text-align:center">'.$this->Form->control('action.'.$k, ['label'=>false,'value' => $menu['link'], 'checked' => $saasAuthGroup['action']? in_array($menu['link'], $saasAuthGroup['action']):false]).'</td>';

						foreach ($advPermissionCategories as $category) {
							$setting = $advPermissions[$k][$category];
							// $this->log(var_export($setting, true));
							echo '<td style="text-align:center; border-left: 1px solid #ccc">';
							if(!empty($advPermissions[$k][$category])){
								$modelName = explode('.', $setting['policyName']);
								echo $this->Form->control('advance.'.$setting['policyName'], [
									'label' => false,
							        'checked' => !empty($saasAuthGroup['advance'][$modelName[0]][$modelName[1]]) 
							            ? $saasAuthGroup['advance'][$modelName[0]][$modelName[1]]
							            : $setting['default']
								]);
							}
							echo '</td>';
						}
						echo '</tr>';
						
					}
				?>
				</table>
			</td>
		</tr>
	</table>
<?= $this->Form->end() ?>
<?php if(!empty($advancePermission['edit'])|| $advancePermission =='grant_all'){ ?>
<div class="w2ui-buttons" style="margin: 10px 0px">
    <button class="w2ui-btn" name="save" onclick="edit();">確定修改</button>
</div>
<?php } ?>
<script>
	function edit(){
		jQuery.ajax({
			url: 'edit/<?php echo $saasAuthGroup->id?>',
			type: 'POST',
			dataType: 'json',
			data: jQuery('#formEdit').serialize(),
			success: function(data) {
				//called when successful
				if(data.status == 'ok'){
					w2ui['layout'].hide('main');
                    w2ui['layout'].show('top');
                    jQuery('#back_btn').hide();
                    w2ui['grid'].reload();
					toastr.success('成功修改');
				}
				else{
					w2alert(data.status);
				}
			},

		});
	}
	jQuery('#chk_all_act').on('change', function(event) {
		if(jQuery(this).is(":checked")){
			jQuery('input[id^=action]').prop('checked', 'checked');
			jQuery('input[id^=advance]').prop('checked', 'checked');
		}else{
			jQuery('input[id^=action]').removeAttr('checked');
			jQuery('input[id^=advance]').removeAttr('checked');
		}
	});
	jQuery('input[id^=action]').on('change', function(event) {
		let id = jQuery(this).attr('id');
		if(jQuery(this).is(":checked")){
			jQuery(document).find('input[id^='+id+'-]').prop('checked', 'checked');
			let regex = /action([0-9]{1,})-[0-9]{1,}/
			let match = regex.exec(id);
			if(match != null){
				if(match[1] != null && match[1] != undefined){
					jQuery('#action'+match[1]).prop('checked', 'checked');
				}
			}
			jQuery(this).closest('tr').find('input[id^=advance]').prop('checked', 'checked');
		}else{
			jQuery(document).find('input[id^='+id+'-]').prop('checked', false);
			jQuery(document).find('input[id^='+id+'-]').closest('tr').find('input[id^=advance]').prop('checked',false);
			jQuery(this).closest('tr').find('input[id^=advance]').prop('checked', false);
		}
	});
	
</script>
