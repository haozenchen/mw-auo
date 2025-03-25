<?php echo $this->App->safeScript('cookie') ?>

<?php
	$loginAutoFill = empty($loginAutoFill)? 0:1;
	if($loginAutoFill == 1) {
		$autocomplete = '';
	}else{
		$autocomplete = ' autocomplete="off"';
	}
?>

<script language="JavaScript">

    var isForceMfa = '<?= h($forceMfa->value) ?>';

    function clear_login() {
        $('user_username').value = "";
        $('user_passwd').value = "";
        $('mfaCodeInput').value = "";
        return false;
    }

    function initial() {
        <?php if(empty($chg_pwd)){ ?>
        if ($('user_username').value == "") {
            var uid = Cookie.get('femasUid');
            if (uid != null) {
                $('user_username').value = uid;
                $('user_passwd').focus();
            } else {
                $('user_username').focus();
            }
        } else {
            $('user_username').focus();
        }

        if (Cookie.get('femasRem') == 1) {
            $('remember').checked = true;
        }
        <?php }?>
    }

    function new_login() {
        jQuery('#flashMessage').empty();
        if ($('user_username').value == "" || $('user_passwd').value == "") {
            jQuery('#flashMessage').html('<div class="errMsg">請勿輸入空值</div>').addClass('error');
            return false;
        } else if (isForceMfa == 1) {
            jQuery.ajax({
                data: jQuery('#login').serialize(),
                type: 'POST',
                dataType: 'json',
                async: false,
                url: '<?php echo $this->Url->build(['controller' => 'SaasAdmins', 'action' => 'checkMfa', 'admin' => false]); ?>',

                success: function(response) {
                    if (response.isMfa == 1) {
                        jQuery('#orglogin').hide();
                        jQuery('#mfaDiv').show();
                        jQuery('#mfaCodeInput').focus();
                    }else if(response.isMfa == -1){
                        jQuery('#flashMessage').html('<div class="errMsg">需先請管理者創建MFA備用碼</div>').addClass('error');
                        clear_login();
                    }else if(response.isMfa == null){
                        jQuery('#flashMessage').html('<div class="errMsg">帳號不存在</div>').addClass('error');
                        clear_login();
                    } else {
                        login_submit();
                    }
                }
            });
        } else {
            login_submit();
        }
    }
    function chgPassword(){
        jQuery.ajax({
            url: '<?php echo $this->Url->build(['controller' => 'SaasAdmins', 'action' => 'chgPwd', 'admin' => false]); ?>',
            data: jQuery('#login').serialize(),
            type: 'POST',
            async: false,
            dataType: 'json',
            success: function(response) {
                if(response.status != 'success'){
                    jQuery('#flashMessage').html('<div class="errMsg">'+response.msg+'</div>').addClass('error');
                }else{
                    jQuery('#flashMessage').html('<div class="errMsg">密碼更新完成</div>').removeClass('error');
                    jQuery('#loginDiv').show();
                    jQuery('#chgDiv').hide();
                }
            }
        });
    }
    function mfacheck() {
        jQuery.ajax({
            url: '<?php echo $this->Url->build(['controller' => 'SaasAdmins', 'action' => 'checkMfaCode', 'admin' => false]); ?>',
            data: jQuery('#login').serialize() + '&data[mfaCode]=' + jQuery('#mfaCodeInput').val(),
            type: 'POST',
            async: false,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response.isMfaPass == 1) {
                    login_submit();
                } else {
                    jQuery('#flashMessage').html('<div class="errMsg">帳號/密碼或兩階段驗證碼錯誤</div>');
                    jQuery('#orglogin').show();
                    jQuery('#mfaDiv').hide();
                    clear_login();
                }
            }
        });
    }

    function login_submit() {
            Cookie.erase('femasUid');
            Cookie.erase('femasRem');
            if (jQuery('remember').prop('checked')) {
                Cookie.set('femasUid', $('user_username').value, 7);
                Cookie.set('femasRem', 1, 7);
            }
            jQuery('#login').submit();

    }

    jQuery(function() {
        jQuery(document).on('click', '#submitBtn', function(e) {
            e.preventDefault();
            new_login();
        });

        jQuery(document).on('click', '#submitPwdBtn', function(e) {
            e.preventDefault();
            chgPassword();
        });

        jQuery(document).on('click', '#clearBtn', function() {
            clear_login();
        });

        jQuery('input').on('keydown', function(e) {
            var id = jQuery(this).attr('id');

            if (e.keyCode == 13) { //13 is enter
                if (id == 'user_username' || id == 'user_passwd') {
                    new_login();
                } else if (id == "mfaCodeInput" || id == "mfaSend") {
                    e.preventDefault();
                    mfacheck();
                } else {
                    e.preventDefault();
                }
            }
        });

        jQuery('#mfaSend').click(function() {
            mfacheck();
        });

        jQuery('#mfaCancel').click(function() {
            $('mfaCodeInput').value = '';
            jQuery('#orglogin').show();
            jQuery('#mfaDiv').hide();
        });
    })

</script>
<div class="container-fluid h-100">
    <div class="row row-cols-1 row-cols-lg-2 h-100">
        <div class="col m-auto justify-content-center align-items-center d-none d-lg-flex">
            <div class=" img-fluid mainImg">
                <img src="<?= $this->Url->build('/img/welcome/img-crm-dealer_2x.png'); ?>" alt="" class="img-fluid">


            </div>
        </div>
        <div class="col m-auto d-flex justify-content-center align-items-center">


                <?php echo $this->Form->create(null, [
                    'id' => 'login',
                    'class' => 'loginForm',
                    'url' => ['controller' => 'SaasAdmins', 'action' => 'login', 'admin' => false],
                    'autocomplete' => $autocomplete
                ]); ?>

                <div id="orglogin" class="loginBox">
                    <div class="comName">鋒形科技</div>
                    <div class="dealerName">AUO同步資料平台</div>
                    <div class="row row-span direct_msg">
                        <div class="col-4 colLabel d-none d-sm-flex"></div>
                        <div id="flashMessage" class="col colInput">
                            <?= $this->Flash->render() ?>
                        </div>
                    </div>
                    <div id="loginDiv" style="<?php echo (!empty($chg_pwd))? 'display: none':''?>">
                        <div class="row row-span">
                            <div class="col-4 colLabel">
                                <label for="user_username" class="labelStyle">帳號</label>
                            </div>
                            <div class="col-7 colInput">
                                <?php echo $this->Form->control('SaasAdmin.username', [
                                    'id' => 'user_username',
                                    'label' => false,
                                    'class' => 'userInput',
                                    'placeholder' => '請輸入帳號',
                                ]); ?>
                            </div>
                        </div>
                        <div class="row row-span">
                            <div class="col-4 colLabel">
                                <label for="user_passwd" class="labelStyle">密碼</label>
                            </div>
                            <div class="col-7 colInput">
                                <?php echo $this->Form->control('SaasAdmin.passwd', [
                                    'id' => 'user_passwd',
                                    'label' => false,
                                    'class' => 'userInput',
                                    'size' => '15', 'maxlength' => '20', 'type'=>"password",
                                    'placeholder' => '請輸入密碼',
                                ]); ?>
                            </div>
                        </div>
                        <div class="row row-span row-phone">
                            <div class="col-4 colLabel d-none d-sm-flex"></div>
                            <div class="col-7 colInput">
                                <div class="remember-group">
                                    <?php echo $this->Form->control('remember', [
                                        'id' => 'user_passwd',
                                        'value' => 1, 'type' => 'checkbox', 'label' => '','class' => 'rememberCheckbox',
                                    ]); ?>
                                    <label for="remember" class="remLab">記住帳號</label>
                                </div>
                            </div>
                        </div>

                        <div class="row span-space"></div>
                        <div class="row row-btn btn-groups">
                            <div class="col-4 colLabel btn-submit-groups">
                                <div id="clearBtn" class="btn clearBtn">重填</div>
                            </div>
                            <div class="col-7 colInput">
                                <button id="submitBtn" type="button" class="btn submitBtn">送出</button>
                            </div>
                        </div>
                    </div>
                    <div id="chgDiv" style="<?php echo (empty($chg_pwd))? 'display: none':''?>">
                        <div class="row row-span">
                            <div class="col-4 colLabel">
                                <label for="new_passwd" class="labelStyle">新密碼</label>
                            </div>
                            <div class="col-7 colInput">
                                <?php echo $this->Form->control('new_passwd', [
                                    'id' => 'new_passwd',
                                    'label' => false,
                                    'class' => 'userInput',
                                    'size' => '15', 'maxlength' => '20', 'type'=>"password",
                                    'placeholder' => '請輸入新密碼',
                                    'autocomplete' => 'new-password'
                                ]); ?>
                            </div>
                        </div>
                        <div class="row row-span">
                            <div class="col-4 colLabel">
                                <label for="confirm_new_passwd" class="labelStyle">再次確認新密碼</label>
                            </div>
                            <div class="col-7 colInput">
                                <?php echo $this->Form->control('confirm_new_passwd', [
                                    'id' => 'confirm_new_passwd',
                                    'label' => false,
                                    'class' => 'userInput',
                                    'size' => '15', 'maxlength' => '20', 'type'=>"password",
                                    'placeholder' => '再次確認新密碼',
                                    'autocomplete' => 'new-password'
                                ]); ?>
                            </div>
                        </div>
                        <div class="row span-space"></div>
                        <div class="row row-btn btn-groups">
                            <div class="col-4 colLabel btn-submit-groups"></div>
                            <div class="col-7 colInput">
                                <button id="submitPwdBtn" type="button" class="btn submitBtn">送出</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="mfaDiv" class="mfaBox" style="display:none;">
                    <div class="mfa-phone-box">
                        <div class="comName mafComName">鋒形科技</div>
                        <div class="row mfa-row">
                            <div class="col-4 colLabel d-none d-lg-flex mfa-col-ctrl"></div>
                            <div class="col-7 colInput mfa-col-ctrl">
                                <div class="mfaImgBox">
                                    <img src="<?= $this->Url->build('/img/welcome/img-crm-mfa_2x.png'); ?>"  class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="row row-span">
                            <div class="col-4 colLabel">
                                <label for="mfaCodeInput" class="mafLabel">兩階段驗證碼</label>
                            </div>
                            <div class="col-7 colInput">
                                <input type="text" name="data[mfaCode]"  id="mfaCodeInput" s flag="focus_input" placeholder="<?php echo __('輸入驗證碼', true); ?>" class="mafInput" />
                            </div>
                        </div>
                        <div class="maf-space"></div>
                        <div class="row row-btn btn-groups">
                            <div class="col-4 colLabel btn-submit-groups">
                                <div class="btn clearBtn" id="mfaCancel">
                                    <?php echo __('取消', true); ?>
                                </div>
                            </div>
                            <div class="col-7 colInput">
                                <button class="btn mafBtn" type="button" id="mfaSend">
                                    <?php echo __('驗證', true); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>