<div>
<form id="formAdd">
<div id="import_hint" class="content">
	<table class="Classical" align="center" width="100%">
		<tr>
			<td class="title right" style="white-space: nowrap;"><?php echo __('欲執行AUO資料', true); ?></td>
			<td class="content left">
				<?php
					$opt = [
						'員工基礎資料表'=>'user',
						'員工進階資料表'=>'user2',
						'員工學歷資料表'=>'edu',
						'員工經歷資料表'=>'exp',
						'生效組織資料表'=>'dep',
						'簽核主管資料表'=>'appover'
					];
				?>
				<?php foreach ($opt as $key => $value) { ?>
					<div class="d-flex align-items-center">
						<input class="mr-1" type="checkbox" checked id="<?php echo $value?>" name="<?php echo $value?>" />
						<label for="<?php echo $value?>"><?php echo $key ?></label>
					</div>
				<?php }?>
			</td>
		</tr>
	</table>
</div>
</form>
</div>
<script>
    function add(){
    	w2popup.close();
		w2utils.lock('body', '同步中，請稍後...', true);
        jQuery.ajax({
			url: '<?php echo $this->Url->build(['controller' => 'SyncRecords', 'action' => 'do_sync']); ?>',
			type: 'POST',
			dataType: 'json',
			data: jQuery('#formAdd').serialize(),
			success: function(data) {
				w2utils.unlock('body');
                if(data.status == 'ok'){
                	toastr.success('同步結束');
                    w2ui['grid'].reload();
				}
                else{
                    w2alert(data.status);
                }
			},

		});
    }
</script>