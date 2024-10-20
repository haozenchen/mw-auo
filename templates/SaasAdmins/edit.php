<?= $this->Form->create($saasAdmin,['id'=>'formEdit']) ?>
<div id="import_hint" class="content">
	<table class="Classical" align="center" width="100%">
		<tr>
			<td class="title right"><?php echo __('姓名', true); ?></td>
			<td class="content left">
			<?php
		        echo $this->Form->control('name',['label'=>false, 'class'=>'textBlack', 'size'=>'30', 'maxlength'=>'24','autocomplete'=>'off']);
		    ?>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('帳號', true); ?></td>
			<td class="content left">
			<?php
		        echo $this->Form->control('username',['label'=>false, 'class'=>'textBlack', 'size'=>'30', 'maxlength'=>'24','autocomplete'=>'off']);
		    ?>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('密碼', true); ?></td>
			<td class="content left">
			<?php
		        echo $this->Form->control('passwd',['id' =>'Passwd', 'label'=>false, 'class'=>'textBlack', 'size'=>'30', 'maxlength'=>'32','autocomplete'=>'new-password']);
		    ?>
			<a href="javascript:void(0)" onclick="$('Passwd').value = '';"><?php echo __('清空密碼', true)?></a>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('密碼到期日', true); ?></td>
			<td class="content left">
				<?php
			        echo $this->Form->control('pwd_expired',['label'=>false, 'class'=>'textBlack', 'type'=>'date']);
			    ?>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('啟用', true); ?></td>
			<td class="content left">
				<?php
			        echo $this->Form->control('active',['label'=>false, 'class'=>'textBlack']);
			    ?>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('MFA備用碼', true); ?></td>
			<td class="content left">
				<div id ="mfa_block" >
					<?php if(!empty($BackupCodes)){ ?>
						<div class="my-3">
							<?php foreach ($BackupCodes as $key => $code) {
								$used =	!empty($code->used)? 'text-decoration:line-through 1.5px;': '';
								echo '<span class="mx-1 p-1" style="border:1px dotted black; font-size:14px; font-weight:bold; background:lightgrey;'. $used .'">';
								echo $code->passwd;
								echo '</span>';
							} ?>
						</div>
					<?php } ?>
				</div>
				<div>
					<button class="w2ui-btn" type="button" onclick="add_emergency_codes()">
                        重新產生備用碼
					</button>
				</div>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('套用授權群組', true); ?></td>
			<td class="content left">
				<?php echo $this->Form->control('group', [
				    'type' => 'select',
				    'options' => $authGroupOpts, // 這是您的選項數據
				    'multiple' => 'checkbox', // 使用多選 checkbox
				    'label' => false, // 不顯示標籤
				    'class' => 'textBlack', // CSS class
				    'style' => 'vertical-align: middle;margin-right:5px',
				    'legend' => false // 不顯示傳說文字
				]); ?>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('帳號建立', true); ?></td>
			<td class="content left">
				<?php echo $saasAdmin->created->format('Y-m-d H:i:s')?>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('最後修改', true); ?></td>
			<td class="content left">
				<?php echo $saasAdmin->modified->format('Y-m-d H:i:s')?>
			</td>
		</tr>
	</table>
</div>
<?= $this->Form->end() ?>
<?php if(!empty($advancePermission['edit'])|| $advancePermission =='grant_all'){ ?>
<div class="w2ui-buttons" style="margin: 10px 0px">
    <button class="w2ui-btn" name="save" onclick="edit();">確定修改</button>
</div>
<?php }?>
<script>
    function edit(){
        jQuery.ajax({
			url: 'edit/<?php echo $saasAdmin->id?>',
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

	function add_emergency_codes(){
        w2confirm('確定產生新備用碼嗎?')
        .yes(function(){
            w2utils.lock('body', '產生中...', true);
            jQuery.ajax({
                url: 'add_emergency_codes/<?php echo $saasAdmin->id?>',
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    w2utils.unlock('body');
                    if(data.status == 'ok'){
                        w2alert('產生成功').ok(function(){
							if(data.new){
								// $j('#mfa_block').html('');
								let new_codes = `<div class="my-3">`;
								for($i=0; $i< data.new.length; $i ++){
									new_codes +=`
										<span class="mx-1 p-1" style="border:1px dotted black; font-size:14px; font-weight:bold; background:lightgrey;">
											${data.new[$i]}
										</span>
									`
								}
								new_codes += `</div>`;
								$j('#mfa_block').html(new_codes);
							}
						});
                    }else{
                        w2alert('產生失敗');
                    }
                },
            });
        })
        .no(function(){
            return false;
        })
    }

</script>