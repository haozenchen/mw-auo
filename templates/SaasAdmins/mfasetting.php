<style type="text/css">
  #genMfaImg,#triggerMfa{
    margin-top: 20px;
      background-color: #f2f2f2;
      display: inline-block;
      padding: 2px 10px;
      border: 1px solid #6666;
      cursor: pointer;
  }

</style>

<table class="Classical" cellpadding="0" width="100%" align="center">
  <tr>
    <td class="title right"><?php echo __('使用者', true); ?></td>
    <td class="content left"><?php echo $accountData['SaasAdmin']['name']?></td>
  </tr>
  <tr>
    <td class="title right"><?php echo __('帳號', true); ?></td>
    <td class="content left"><?php echo $accountData['SaasAdmin']['username']?></td>
  </tr>
  <tr>
          <td class="title right"><?php echo __('搭配Authy app使用認證', true); ?></td>
          <td class="content left">
            <?php echo $form->input('is_mfa', array('id' => 'isMfa', 'options' =>array(1 => __('啟用', true), 0 => __('停用', true)), 'legend'=>false, 'label' => false, 'type'=>'radio', 'class'=>'textBlack', 'between' =>false, 'div' => false)) ?>
            <div id="mfaDiv" style="display:none;">
        <div style="margin-top:10px;">
          <span id="genMfaImg"><?php echo __('產生QR Code', true); ?></span>
        </div>
        <div id="mfaTriggerDiv" style="display:none;">
          <div id="mfaImgLoad" style="display:inline-block; margin-top:15px;"><img src="/img/loading_ajax.gif"></div>
          <div id="mfaImg" style="display:none; margin-top:15px;"></div>
          <div style="margin-bottom:5px;"><?php echo __('請輸入此QR Code之驗證碼', true); ?></div>
          <div><input type="text" id="mfaCode" placeholder="<?php echo __('輸入驗證碼', true); ?>" flag="focus_input"><span style="margin-left:10px;" id="triggerMfa"><?php echo __('確認', true)?></span></div>
        </div>
      </div>
          </td>
        </tr>
</table>

<script type="text/javascript">
  var mfa_Status = '';
  $j(function(){
    jQuery.ajax({
    url: '<?php echo Router::Url(array("controller" => "SaasAdmins", "admin" => false, "action" => "getMfa"), TRUE); ?>',
    type: 'POST',
    dataType : 'json',
    async: false,
    beforeSend : function() {
      jQuery('#loading').show();
    },
    success: function(response) {
      jQuery('#loading').hide();
      mfa_Status = response.mfaStatus;
      if(response.mfaStatus!='not_open') {
        if(response.mfaStatus=='have_mfa') {
          jQuery('#genMfaImg').parent().hide();
          jQuery('#mfaImg').html(response.imgStr);
          jQuery('#mfaImg').find('img').load(function() {
            jQuery('#mfaImgLoad').hide();
            jQuery('#mfaImg').css({'display': 'inline-block'});
          });
          jQuery('#mfaTriggerDiv').show();
        } else if(response.mfaStatus=='not_gen') {
          jQuery('#genMfaImg').parent().show();
          jQuery('#mfaTriggerDiv').hide();
        }
        if(response.isMfa==1) {
          jQuery('#IsMfa1').prop('checked', true);
          jQuery('#IsMfa0').prop('checked', false);
          jQuery('#mfaDiv').show();
        } else if(response.isMfa==0) {
          jQuery('#IsMfa1').prop('checked', false);
          jQuery('#IsMfa0').prop('checked', true);
          jQuery('#mfaDiv').hide();
        }
      }
    }
  });
    jQuery('#genMfaImg').click(function() {
      var thisId = jQuery(this).attr('id')
      jQuery.ajax({
        url: '<?php echo Router::Url(array("controller" => "SaasAdmins", "admin" => false, "action" => "genMfa"), TRUE); ?>',
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
          jQuery('#mfaImg').html(response.imgStr);
          jQuery('#mfaImg').find('img').load(function() {
            jQuery('#mfaImgLoad').hide();
            jQuery('#mfaImg').css({'display': 'inline-block'});
          });
          
          if(thisId=='genMfaImg') {
            jQuery('#genMfaImg').parent().hide();
            jQuery('#mfaTriggerDiv').show();
          }
        }
      });
    });
   
    jQuery('#IsMfa1').click(function() {
      if(jQuery(this).prop('checked')){
         if(mfa_Status=='have_mfa'){
          w2confirm('確定啟用兩階段驗證?')
          .yes(function () {
              disableMfa();
              jQuery('#mfaDiv').show();
              jQuery('#IsMfa1').css('pointer-events','none');
              jQuery('#IsMfa0').css('pointer-events','auto');
          })
          .no(function(){
              jQuery('#IsMfa1').prop('checked', false);
              jQuery('#IsMfa0').prop('checked', true);
          })
        }
        else{
          jQuery('#mfaDiv').show();
        }
      }
    });

    jQuery('#IsMfa0').click(function() {
      if(jQuery(this).prop('checked')) {
         w2confirm('確定停用兩階段驗證?')
        .yes(function () {
            disableMfa();
            jQuery('#mfaDiv').hide();
            jQuery('#IsMfa0').css('pointer-events','none');
            jQuery('#IsMfa1').css('pointer-events','auto');
        })
        .no(function(){
              jQuery('#IsMfa1').prop('checked', true);
              jQuery('#IsMfa0').prop('checked', false);
        })
      } 
    });
    function disableMfa(){
      var isMfa = jQuery('#IsMfa1').prop('checked')?1:0;
      jQuery.ajax({
        url:'<?php echo Router::Url(array("controller" => "SaasAdmins", "admin" => false, "action" => "mfasetting"), TRUE); ?>',
        data:'data[isMfa]='+isMfa,
        type: 'POST',
        async: false,
        dataType : 'json',
        success :function(){

        }
      })
    }
    jQuery('#triggerMfa').click(function() {
      var isMfa = jQuery('#IsMfa1').prop('checked')?1:0;
      jQuery.ajax({
        url: '<?php echo Router::Url(array("controller" => "SaasAdmins", "admin" => false, "action" => "checkMfaCode"), TRUE); ?>',
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
            w2ui['layout'].hide('main');
            w2ui['layout'].show('top');
            jQuery('#back_btn').hide(); 
            w2ui['grid'].reload();
            alert('<?php echo __("啟用兩階段認證(MFA)成功", true); ?>');
          } else {
            alert('<?php echo __("認證碼錯誤", true); ?>');
          }
        }
      });
    });
  })
</script>