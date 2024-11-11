<?php echo $this->Html->script('div') ?>
<?php echo $this->Html->script('screen') ?>
<?php echo $this->Html->css('classical') ?>
<?php echo $this->Html->css('emma.advance') ?>

<style type="text/css">
	.w2ui-buttons{
		position: fixed;
		top: 0px;
		right: 25px;
	}

	#triggerMfa,#resetMfa{
		background-color: #f2f2f2;
		display: inline-block;
		padding: 2px 10px;
		border: 1px solid #6666;
		cursor: pointer;
		margin: 5px
 	}

	input[type="radio"]{
		vertical-align:middle;
	}


</style>
<div style="padding: 10px; margin-left: 10px; text-align: left; font-family: 'Noto Sans TC', sans-serif; font-weight: 900; color: #666">
	<?php echo __('我的帳號', true); ?> <span id="navi_title"></span>
</div>
<div id="edit">
	<?= $this->Form->create($saasAdmin,['id'=>'formEdit']) ?>
	<table class="Classical" align="center" width="100%">
		<tr>
			<td class="title right"><?php echo __('姓名', true); ?></td>
			<td class="content left">
				<?php echo $saasAdmin['name'] ?>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('帳號', true); ?></td>
			<td class="content left">
				<?php echo $saasAdmin['username'] ?>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('密碼', true); ?></td>
			<td class="content left"><a href="javascript:void(0)" id="chg_pwd"><?php echo __('變更', true); ?></a>
				<div id="pass_div" style="color: #333; display: none">
					<div>
						<?php echo __('請遵守密碼建立原則：', true);?>
						<ol>
							<li><?php echo __('長度至少12位以上', true);?></li>
							<li><?php echo __('不得與前兩次密碼相同', true);?></li>
							<li><?php echo __('密碼必須包含以下四種符號其中三種組成，英文字母大寫﹑小寫﹑數字﹑特殊符號(@﹑$﹑$﹑~...)', true);?></li>
						</ol>
					</div>
					<?php
				        echo $this->Form->control('orig_passwd',['label'=>false, 'class'=>'textBlack', 'size'=>'30', 'maxlength'=>'32','placeholder' => __('請輸入原始密碼', true), 'type' => 'password']);
				    ?>
					<?php
				        echo $this->Form->control('new_passwd',['label'=>false, 'class'=>'textBlack my-3', 'size'=>'30', 'maxlength'=>'32','placeholder' => __('請輸入新密碼', true), 'type' => 'password']);
				    ?>
				    <?php
				        echo $this->Form->control('confirm_new_passwd',['label'=>false, 'class'=>'textBlack', 'size'=>'30', 'maxlength'=>'32','placeholder' => __('請再次確認新密碼', true), 'type' => 'password']);
				    ?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('套用授權群組', true); ?></td>
			<td class="content left">
				<?php
					foreach ($saasAdmin['group'] as $v) {
						echo '<div>'.$authGroupOpts[$v].'</div>';
					}
				?>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('建立時間', true); ?></td>
			<td class="content left">
				<?php echo $saasAdmin['created']->format('Y-m-d H:i:s')?>
			</td>
		</tr>
		<tr>
			<td class="title right"><?php echo __('最後修改時間', true); ?></td>
			<td class="content left">
				<?php echo $saasAdmin['modified']->format('Y-m-d H:i:s')?>
			</td>
		</tr>
		<tr>
          <td class="title right"><?php echo __('搭配Authy app使用認證', true); ?></td>
          <td class="content left">
			<div id="mfaDiv">
				<div id="mfaStatus">
					<div class="d-flex align-items-center">
						<div id="mfaChk"></div>
						<div id="resetMfa" style="display: none">重設</div>
					</div>
				</div>
				<div id="mfaTriggerDiv" style="display:none;">
					<div id="mfaImgLoad" style="display:inline-block; margin-top:15px;">
						<img src="<?= $this->Url->build('/img/loading_ajax.gif'); ?>">
					</div>
					<div id="mfaImg" style="display:none;"></div>
					<div class="my-2"><?php echo __('請輸入此QR Code之驗證碼', true); ?></div>
					<div>
						<input type="text" id="mfaCode" placeholder="<?php echo __('輸入驗證碼', true); ?>" flag="focus_input">
						<span style="margin-left:10px;" id="triggerMfa"><?php echo __('確認', true)?></span>
					</div>
				</div>
			</div>
          </td>
        </tr>
	</table>
	<div class="w2ui-buttons" style="margin: 10px 0px">
		<button class="w2ui-btn" name="save" onclick="edit();" style="display: none"><?php echo __('確定修改', true); ?></button>
	</div>
	<?= $this->Form->end() ?>
</div>
<script>

	function edit(){
		if(jQuery('#confirm-new-passwd').val() == '' || jQuery('#new-passwd').val() == '' || jQuery('#orig-passwd').val() == ''){
			w2alert('請填寫所有必要欄位');
		}else if(jQuery('#confirm-new-passwd').val() != jQuery('#new-passwd').val()){
			w2alert('再次確認新密碼錯誤');
		}else if(passRegCheck(jQuery('#new-passwd').val()) == false){
			w2alert('密碼未遵守密碼建立原則');
		}else{
			jQuery.ajax({
				url: 'edit_my_account',
				type: 'POST',
				dataType: 'json',
				data: jQuery('#formEdit').serialize(),
				success: function(data) {
					//called when successful
					if(data.status == 'success'){
						jQuery('#pass_div').hide();
						jQuery('#chg_pwd').show();
						jQuery('#pass_div').find('input').val('');
						w2alert(data.msg);
					}
					else{
						w2alert(data.msg);
					}
				},

			});
		}
	}

	jQuery('#chg_pwd').on('click', function(event) {
		jQuery('#pass_div').show();
		jQuery('button[name=save]').show();
		jQuery(this).hide();
	});


	function passRegCheck(password){
		let regexList = ['.{12,}', '[a-z]{1,}', '[A-Z]{1,}', '[0-9]{1,}', '[^\\w]{1,}'];
		const isMatch = regexList.every(function(rx) {
				let regex = new RegExp(rx);
				return regex.test(password);
			});
		return isMatch;
	}

	var secret = false;
	$j(function(){
		jQuery.ajax({
			url: '<?php echo $this->Url->build(['controller' => 'SaasAdmins', 'action' => 'getMfa']); ?>',
			type: 'POST',
			dataType : 'json',
			async: false,
			beforeSend : function() {
			},
			success: function(response) {
				jQuery('#loading').hide();
				if(response.mfaStatus=='have_mfa') {
					jQuery('#mfaChk').html('兩階段設定完成');
					jQuery('#resetMfa').show();
				} else if(response.mfaStatus=='not_gen') {
					jQuery('#mfaTriggerDiv').show();
					genMfa();
				}
			}
		});

		jQuery('#resetMfa').click(function(){
			w2confirm('確定重設兩階段驗證?')
			.yes(function () {
				jQuery('#mfaTriggerDiv').show();
				jQuery('#mfaStatus').hide();

				jQuery.ajax({
					url: '<?php echo $this->Url->build(['controller' => 'SaasAdmins', 'action' => 'clearMfa']); ?>',
					type: 'POST',
					dataType : 'json',
					success: function(response) {
						if(response.result == 'ok'){
							genMfa();
						}
					}
				});
			})
			.no(function(){
			})
		});

		function genMfa(){
			jQuery.ajax({
				url: '<?php echo $this->Url->build(['controller' => 'SaasAdmins', 'action' => 'genMfa']); ?>',
				type: 'POST',
				dataType : 'json',
				beforeSend : function() {
				jQuery('#loading').show();
				jQuery('#mfaImgLoad').css({'display': 'inline-block'});
				jQuery('#mfaImg').hide();
				},
				success: function(response) {
					jQuery('#loading').hide();
					secret = response.secret;
					var img = '<img src="<?php echo $this->Url->build(['controller' => 'SaasAdmins', 'action' => 'create_qr_code']); ?>/'+secret+'">';
					jQuery('#mfaImg').html(img);
					// jQuery('#mfaImg').html(response.imgStr);
					jQuery('#mfaImg').find('img').load(function() {
						jQuery('#mfaImgLoad').hide();
						jQuery('#mfaImg').css({'display': 'inline-block'});
					});
					
					
				}
			});
		}

		jQuery('#triggerMfa').click(function() {
			var isMfa = jQuery('#isMfa-1').prop('checked')?1:0;
			if(secret){
				jQuery.ajax({
					url: '<?php echo $this->Url->build(['controller' => 'SaasAdmins', 'action' => 'checkMfaCode']); ?>',
					data: 'data[type]=save&data[mfaKey]='+encodeURIComponent(secret)+'&data[mfaCode]='+jQuery('#mfaCode').val()+'&data[isMfa]='+isMfa,
					type: 'POST',
					async: false,
					dataType : 'json',
					beforeSend : function() {
					jQuery('#loading').show();
					},
					success: function(response) {
						jQuery('#loading').hide();
						if(response.isMfaPass==1) {
							toastr.success('啟用兩階段認證(MFA)成功');
							setTimeout(function(){
								location.reload()
							},1000)
						} else {
							toastr.error('認證碼錯誤');
						}
					}
				});
			}else{
				alert('已驗證過，無須再次輸入驗證碼');
			}
		});

		document.getElementById('formEdit').addEventListener('submit', function (event) {
	        event.preventDefault();
	    });
	});
</script>