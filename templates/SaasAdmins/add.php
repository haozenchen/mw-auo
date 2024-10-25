<style>
	#ss .checkbox{
		display:inline-block;
		margin-right: 10px;
	}

	#ss .checkbox > input{
		vertical-align:middle;
	}

	#ss .checkbox > label{
		margin:0 !important;
		margin-left:3px !important;
	}

	input[type="radio"]{
        vertical-align:middle;
    }

	label{
		margin-right:5px;
		margin-bottom:0px !important;
	}

</style>

<?= $this->Form->create($saasAdmin,['id'=>'formAdd']) ?>

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
			<td class="title right"><?php echo __('啟用', true); ?></td>
			<td class="content left">
				<?php
			        echo $this->Form->control('active',['label'=>false, 'class'=>'textBlack', 'type'=>'checkbox']);
			    ?>
			</td>
		</tr>
		<?php if(!empty($forceMfa)){ ?>
				<tr>
					<td class="title right"><?php echo __('一併新增MFA備用碼', true); ?></td>
					<td class="content left">
						<?php echo $this->Form->control('forceMfa', [
						    'type' => 'radio',
						    'options' => [1 => __('是', true), 0 => __('否', true)], // 選項數據
						    'label' => false, // 不顯示標籤
						    'legend' => false, // 不顯示傳說文字
						    'class' => 'textBlack', // 自定義 CSS 樣式
						    'default' => 1, // 設定預設值
						    'div' => false, // 不包裹 div
						]); ?>
					</td>
				</tr>
			<?php } ?>

		<tr>
			<td class="title right"><?php echo __('套用授權群組', true); ?></td>
			<td class="content left">
				<?php echo $this->Form->control('group', [
				    'type' => 'select',
				    'options' => $authGroupOpts, // 這是您的選項數據
				    'multiple' => 'checkbox', // 使用多選 checkbox
				    'label' => false, // 不顯示標籤
				    'class' => 'textBlack', // CSS class
				    'legend' => false // 不顯示傳說文字
				]); ?>
			</td>
		</tr>
	</table>
</div>
<?= $this->Form->end() ?>
<?php if(!empty($advancePermission['add'])|| $advancePermission =='grant_all'){ ?>
<div class="w2ui-buttons" style="margin: 10px 0px">
    <button class="w2ui-btn" name="save" onclick="add();">確定新增</button>
</div>
<?php }?>
<script>
    function add(){
        jQuery.ajax({
			url: 'add',
			type: 'POST',
			dataType: 'json',
			data: jQuery('#formAdd').serialize(),

			success: function(data) {

				//called when successful
                if(data.status == 'ok'){
                    w2ui['layout'].hide('main');
                    w2ui['layout'].show('top');
                    jQuery('#back_btn').hide();
                    w2ui['grid'].reload();
					toastr.success('成功新增');
				}
                else{
                    w2alert(data.status);
                }
			},

		});
    }
</script>