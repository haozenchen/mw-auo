<?php echo $this->element('top_layout'); ?>
<script>
	/**
	 * here we check top menu status and use cookie to set its style
	 */ 
	var setTopMenu = Cookie.get('setTopMenu');	
	if (setTopMenu != null) {
		$(setTopMenu).className = 'topmenuClick';
	}
</script>
<div id="middleLayout">
	<table class="public" cellpadding="0" width="100%">
		<tr>
			<td valign="top" width="100%">
				<?php echo $content_for_layout;?>
			</td>
		</tr>
	</table>
</div>

<script>
	/**
	 * here we erase top menu status
	 */
	/*Cookie.erase('setTopMenu');*/
</script>

<?php echo $this->element('foot_layout'); ?>
<script>
	$j( document ).ready(function() {				
		//page jumper
		$j('body').on('mouseover', 'span.pagecount', function(event){			
			if($j(event.target).parent().find('a:eq(0)').length == 0){
				return false;
			}
			$j(event.target).css({'background-color':'#E2E2E2', 'padding':'3px'});
			$j(event.target).attr('title', '自行輸入頁碼');
		}).on('mouseout', 'span.pagecount', function(event){
			$j('.pagecount').css('background-color', '');	
		}).on('click', 'span.pagecount', function(event){
			//var listDiv = $j('#UpdateDiv').val();
			var listDiv = $j(event.target).parents('div:eq(3)').attr('id');
			if($j(event.target).parent().find('a:eq(0)').length == 0){
				return false;
			}
			if($j(event.target).find('#pageJump').length == 0){ 
			var pagecount = $j(event.target).text().split('/');
			var url = $j(event.target).parent().find('a:eq(0)').attr('href');
			var url2 = url.substr(url.indexOf("page"), url.length);
			if(url2.indexOf("/") != -1){
				url2 = url2.substr(url2.indexOf("/"), url2.length);
			}else{
				url2 = '';
			}
			url = url.substr(0, url.indexOf("page"))+'page:';
			var pinput = $j('<input type="text" id="pageJump" name="pageJump" size="4" /><span> / '+pagecount[1]+'</span>');
			$j(event.target).html(pinput);
			$j(event.target).find('#pageJump').val(pagecount[0]);
			$j(event.target).find("#pageJump").keydown(function(e){
				if (e.keyCode == 13) {
					$j('#loading').show();
					if(parseInt($j(this).val()) != $j(this).val()){
						$j(this).val(1);	
					};					
					$j.ajax({
						type: 'POST',
						url: url+$j(this).val()+url2,
						data:$j(event.target).parent('div').find('#PagingForm').length > 0 ? $j('#' + $j(event.target).parent('div').find('#PagingForm').val()).serialize() : '' + $j(event.target).parent('div').find('#PagingInput').length > 0 ? $j('[id^=' + $j(event.target).parent('div').find('#PagingInput').val() +']').serialize() : '',
						success: function (response) {
							$j('#'+listDiv).html(response);
						},
						complete: function (argument) {
							$j('#loading').hide();
						}
					});
					return false;//prevent other form submit
				}
			});
			}
		});
	})
</script>