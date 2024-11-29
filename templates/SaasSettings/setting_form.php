<?php
    $tagSet['tabName'] = 'tab';
    $tagSet['startTagCode'] = 0;
    $tagSet['selectTagId'] = 'SaasSettingTab';
    $tagMenus = array();
    $tagMenus[0] = array('name'=> '同步設定', 'divname' => 'sync_setting');
    $tagMenus[1] = array('name'=> 'API設定', 'divname' => 'sys_api');
    $tagMenus[2] = array('name'=> '系統設定', 'divname' => 'sys_setting');
    // $tagMenus[3] = array('name'=> '兩階段驗證(MFA)設定', 'divname' => 'mfa_setting');
    $ajaxSubmit = array(
        'update' => 'listing',
        'url' => array('action' => 'setting_form'),
        'loading' => "Element.show('loading');",
        'complete' => "Element.hide('loading');",
    );

?>

<?php
    $newUser = array(
        '1' => __('到職', true)
    );

    $userChange = array(
        '30' => __('一般調動(部門/職等/職稱/主管/工作職掌)', true),
        '51' => __('晉升', true),
        '23' => __('試用期滿', true),
        '24' => __('延長試用', true),
        '2' => __('調職', true),
        '52' => __('降調', true),
        '26' => __('續聘', true),
        '20' => __('轉正職', true),
        '27' => __('轉計時人員', true),
        '25' => __('轉聘用人員', true),
        '22' => __('轉臨時人員', true),
        '21' => __('轉留職停薪', true),
        '3' => __('復職', true),
        '1' => __('到職', true),
    );

    $leavingUser = array('leave' => __('離職', true), 'retire' => __('退休', true), 'layoff' => __('資遣', true), 'decease' => __('在職亡故', true));

    $unpaidUser = array('unpaid' => __('留停', true));
?>
<?php echo $this->Form->create(null, ['id' => 'SettingForm', 'name' => 'SettingForm', 'action'=> 'setting_form']) ?>
<?php echo $this->Form->hidden($tagSet['tabName'], ['value' => $tagSet['startTagCode'], 'id' => 'SaasSettingTab']); ?>
<style type="text/css">
    .Classical label{
        margin: 5px 0px;
    }
    div#femas_test, div#auo_test, div#auo_ids_test{
        cursor: pointer;
        color: blue;
        padding: 5px 10px;
    }
    div#femas_test:hover, div#auo_test:hover, div#auo_ids_test:hover{
        color: #8a8af9;
    }

</style>
<div id="tag_main">
    <div id="tag_menu">
        <div id="head">
            <div id="title"></div>
            <?php foreach ($tagMenus as $b => $tagMenu) : ?>
            <div id="<?php echo $tagSet['tabName'].$b; ?>" onmouseover="checkMouseOver('<?php echo $tagSet['tabName']; ?>', <?php echo $b; ?>, '<?php echo $tagSet['selectTagId']; ?>');" onmouseout="checkMouseOut('<?php echo $tagSet['tabName']; ?>', <?php echo $b; ?>, '<?php echo $tagSet['selectTagId']; ?>'); " onclick="changeTag('<?php echo $tagSet['tabName']; ?>', <?php echo $b;?>, '<?php echo $tagSet['selectTagId']; ?>');" class="<?php echo ($b == $tagSet['startTagCode'])? 'tag3': 'tag1'; ?>">
                <?php echo $tagMenu['name']; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div id="sync_setting">
            <div id="body">
                <table class="Classical" cellpadding="0" width="100%" align="center">
                    <tr>
                        <td class="title right">
                            <?php echo __('AUO資料閥值', true); ?>
                        </td>
                        <td class="content left">
                            <div><?php echo $this->Form->control('SaasSetting.UserMinReference', ['label'=> __('員工基礎資料表', true).'：', 'size' => 3, 'type'=>'text','after'=> '&nbsp;'.__('筆', true), 'value' => $SaasSetting['UserMinReference']]); ?></div>
                            <div><?php echo $this->Form->control('SaasSetting.User2MinReference', ['label'=> __('員工進階資料表', true).'：', 'size' => 3, 'type'=>'text','after'=> '&nbsp;'.__('筆', true), 'value' => $SaasSetting['User2MinReference']]); ?></div>
                            <div><?php echo $this->Form->control('SaasSetting.DepartmentMinReference', ['label'=> __('生效組織資料表', true).'：', 'size' => 3, 'type'=>'text','after'=> '&nbsp;'.__('筆', true), 'value' => $SaasSetting['DepartmentMinReference']]); ?></div>

                            <div style="color:#FF0000"><?php echo __('掃描AUO資料筆數低於閥值，視為異常，不執行同步', true); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="title right">
                            <?php echo __('最大異動閥值', true); ?>

                        </td>
                        <td class="content left">
                            <div><?php echo $this->Form->control('SaasSetting.UserDiffReference', ['label'=> __('員工', true).'：', 'size' => 3, 'type'=>'text','after'=> '&nbsp;'.__('筆', true), 'value' => $SaasSetting['UserDiffReference']]); ?></div>
                            <div><?php echo $this->Form->control('SaasSetting.DepartmentDiffReference', ['label'=> __('組織', true).'：', 'size' => 3, 'type'=>'text','after'=> '&nbsp;'.__('筆', true), 'value' => $SaasSetting['DepartmentDiffReference']]); ?></div>
                            <div style="color:#FF0000"><?php echo __('異動筆數高於閥值，視為異常，不執行同步', true); ?></div>


                        </td>
                    </tr>
                    <tr>
                        <td class="title right">
                            <?php echo __('鋒形對應值設定', true); ?><br>
                            <?php echo __('(多筆逗點分隔)', true); ?>
                        </td>
                        <td class="content left">
                            <div><?php echo $this->Form->control('SaasSetting.NewUser', ['label'=> __('新進', true).'：', 'type'=>'text' , 'value' => $SaasSetting['NewUser']]); ?></div>
                            <div><?php echo $this->Form->control('SaasSetting.UserChange', ['label'=> __('調派', true).'：', 'type'=>'text' , 'value' => $SaasSetting['UserChange']]); ?></div>
                            <div><?php echo $this->Form->control('SaasSetting.LeaveUser', ['label'=> __('離退', true).'：', 'type'=>'text' , 'value' => $SaasSetting['LeaveUser']]); ?></div>
                            <div><?php echo $this->Form->control('SaasSetting.UnpaidLeaveUser', ['label'=> __('留停', true).'：', 'type'=>'text' , 'value' => $SaasSetting['UnpaidLeaveUser']]); ?></div>
                            <div><?php echo $this->Form->control('SaasSetting.Reinstate', ['label'=> __('復職', true).'：', 'type'=>'text' , 'value' => $SaasSetting['Reinstate']]); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="4" class="title right">
                            <?php echo __('同步訊息寄信設定', true); ?>
                            <div><?php echo __('(收件者多筆用分號間隔)', true)?></div>
                        </td>
                        <td class="content left">
                            <div><?php echo $this->Form->control('SaasSetting.mail_host', ['label'=> __('發信伺服器網域：', true), 'type'=>'text','size' => 100, 'value' => $SaasSetting['mail_host']]); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="content left">
                            <div><?php echo $this->Form->control('SaasSetting.email_code', ['label'=> __('MailCode：', true), 'type'=>'text','size' => 100, 'value' => $SaasSetting['email_code']]); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="content left">
                            <div><?php echo $this->Form->control('SaasSetting.email_crt', ['label'=> __('憑證檔：', true), 'type'=>'text','size' => 100, 'value' => $SaasSetting['email_crt']]); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="content left  d-flex">
                            <div><?php echo $this->Form->control('SaasSetting.email_address', ['label'=> __('收件者：', true), 'type'=>'text','size' => 100, 'value' => $SaasSetting['email_address']]); ?></div>
                            <div id="auo_ids_test"><?php echo __('測試', true); ?></div>
                        </td>
                    </tr>

                </table>
            </div>
        </div>

        <div id="sys_api" style="display:none">
            <div id="body">
                <table class="Classical" cellpadding="0" width="100%" align="center">
                    <tr>
                        <td rowspan="3" class="title right">
                            <?php echo __('鋒形Femas', true); ?>
                        </td>
                        <td class="content left">
                            <div><?php echo $this->Form->control('SaasSetting.FemasHost', ['label'=> __('網域：', true), 'type'=>'text','size' => 100, 'value' => $SaasSetting['FemasHost']]); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="content left">
                            <div><?php echo $this->Form->control('SaasSetting.FemasToken', ['label'=> __('Token：', true), 'type'=>'text','size' => 100, 'value' => $SaasSetting['FemasToken']]); ?></div>
                        </td class="content left">
                    </tr>
                    <tr>
                         <td class="content left d-flex">
                            <?php
                                $fs_action = ['su_users' => 'su_users', 'su_departments' => 'su_departments', 'su_countrys' => 'su_countrys', 'su_education_types' => 'su_education_types', 'su_user_changes' => 'su_user_changes'];
                                echo $this->Form->control('fs_test_action', [
                                    'options' => $fs_action,
                                    'label' => __('test action：')
                                ]);
                            ?>
                            <div id="femas_test"><?php echo __('測試', true); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="6" class="title right">
                            <?php echo __('友達AUO', true); ?>
                        </td>
                        <td class="content left">
                            <div><?php echo $this->Form->control('SaasSetting.AUOHost', ['label'=> __('網域：', true), 'type'=>'text','size' => 100, 'value' => $SaasSetting['AUOHost']]); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="content left">
                            <div><?php echo $this->Form->control('SaasSetting.AUOip', ['label'=> __('認證IP：', true), 'type'=>'text','size' => 20, 'value' => $SaasSetting['AUOip']]); ?></div>
                        </td class="content left">
                    </tr>
                    <tr>
                        <td class="content left">
                            <div><?php echo $this->Form->control('SaasSetting.AUOpwd', ['label'=> __('認證密碼：', true), 'type'=>'text','size' => 100, 'value' => $SaasSetting['AUOpwd']]); ?></div>
                        </td class="content left">
                    </tr>
                    <tr>
                        <td class="content left">
                            <div><?php echo $this->Form->control('SaasSetting.AUOguid', ['label'=> __('guid：', true), 'type'=>'text','size' => 100, 'value' => $SaasSetting['AUOguid']]); ?></div>
                        </td class="content left">
                    </tr>
                    <tr>
                        <td class="content left">
                            <div><?php echo $this->Form->control('SaasSetting.AUOCompanyId', ['label'=> __('CompanyId：', true), 'type'=>'text','size' => 100, 'value' => $SaasSetting['AUOCompanyId']]); ?></div>
                        </td class="content left">
                    </tr>
                    <tr>
                         <td class="content left d-flex">
                            <?php
                                $auo_action = ['HR_org_data_all' => 'HR_org_data_all', 'HR_paitw01_o1' => 'HR_paitw01_o1', 'HR_paitw05_o7' => 'HR_paitw05_o7', 'HR_paitw05_o1' => 'HR_paitw05_o1', 'HR_paitw05_o2' => 'HR_paitw05_o2','femas_approver' => 'femas_approver', 'HR_paitw05_act' => 'HR_paitw05_act'];
                                echo $this->Form->control('auo_test_action', [
                                    'options' => $auo_action,
                                    'label' => __('test action：'),
                                    'div' => false
                                ]);
                            ?>
                            <div id="auo_test"><?php echo __('測試', true); ?></div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div id="sys_setting" style="display:none">
            <div id="body">
                <table class="Classical" cellpadding="0" width="100%" align="center">
                    <tr>
                        <td class="title right"><?php echo __('閒置幾秒自動登出', true); ?></td>
                        <td class="content left">
                            <div class=" d-flex align-items-center">
                                <?php echo $this->Form->control('SaasSetting.LoginLifeTime', ['label'=> false, 'type'=>'text', 'default'=> 1440, 'size' => 6, 'value' => $SaasSetting['LoginLifeTime']]); ?>
                                <div class="ml-2"><?php echo __('秒', true)?></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="title right"><?php echo __('紀錄保留時間', true); ?></td>
                        <td class="content left">
                            <div class=" d-flex align-items-center">
                                <?php echo $this->Form->control('SaasSetting.LogExpired', ['label'=> false, 'type'=>'text', 'default'=> 90, 'size' => 6, 'value' => $SaasSetting['LogExpired']]); ?>
                                <div class="ml-2"><?php echo __('天', true)?></div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div id="mfa_setting"  style="display:none">
            <div id="body">
                <table class="Classical" cellpadding="0" width="100%" align="center">
                <tr>
                    <td class="title right"><?php echo __('允許帳戶開通兩階段驗證功能', true); ?></td>
                    <td class="content left">
                        <?php echo $this->Form->control('SaasSetting.mfaSetting', [
                            'id' => 'isMfa',
                            'options' => [1 => __('啟用', true), 0 => __('停用', true)],
                            'legend'=>false,
                            'label' => false,
                            'type'=>'radio',
                            'class' => 'textBlack',
                            'between' =>false,
                            'div' => false,
                            'default' => $SaasSetting['mfaSetting']
                        ]); ?>

                    </td>
                </tr>
                <tr id="forceMfaBlock">
                    <td class="title right">
                        <?php echo __('強制開啟MFA兩階段認證', true); ?>
                        <br>
                        (啟用後請至帳戶管理為帳號新增備用碼)
                    </td>
                    <td class="content left">
                        <div id="add_emergency_codes_all">
                            <button class="w2ui-btn" type="button" onclick="add_emergency_codes()">
                                一併為所有帳號產生新備用碼
                            </button>
                        </div>
                    </td>
                </tr>
            </table>
            </div>
        </div>
        <?php if(!empty($advancePermission['edit']) || $advancePermission == 'grant_all'){ ?>
		<div class="rightBtn">
			<table align="right">
				<tr>
					<td>
                        <button id="submitBtn" type="button" name="data[save]" class="w2ui-btn">儲存設定</button>
					</td>
				</tr>
			</table>
		</div>
		<?php } ?>
    </div>
</div>

<?php echo $this->Form->end(); ?>
<?php echo $this->element('tag_menus', array('tagMenus'=>$tagMenus, 'tagSet'=>$tagSet)); ?>
<script>
    jQuery(function() {
        jQuery(document).on('click', '#submitBtn', function(e) {
            e.preventDefault();
            jQuery.ajax({
                data: jQuery('#SettingForm').serialize(),
                type: 'POST',
                dataType: 'json',
                async: false,
                url: '<?php echo $this->Url->build(['controller' => 'SaasSettings', 'action' => 'setting_form']); ?>',
                success: function(response) {
                    toastr.success('成功修改');
                }
            });
        });

        jQuery(document).on('click', '#femas_test', function(e) {
            e.preventDefault();
            w2utils.lock('body', '測試中...', true);
            jQuery.ajax({
                data: jQuery('#SettingForm').serialize(),
                type: 'POST',
                dataType: 'json',
                url: '<?php echo $this->Url->build(['controller' => 'SaasSettings', 'action' => 'femas_test']); ?>',
                success: function(response) {
                    w2utils.unlock('body');
                    var text = '';
                    var title = '';
                    if(response.status == 'ok'){
                        text = JSON.stringify(response.data);
                    }else{
                        text = response.msg;
                    }
                    w2popup.open({
                        width: 750, height: 400,
                        title: 'Response',
                        body: text
                    });
                }
            });
        });

        jQuery(document).on('click', '#auo_test', function(e) {
            e.preventDefault();
            w2utils.lock('body', '測試中...', true);
            jQuery.ajax({
                data: jQuery('#SettingForm').serialize(),
                type: 'POST',
                dataType: 'json',
                url: '<?php echo $this->Url->build(['controller' => 'SaasSettings', 'action' => 'auo_test']); ?>',
                success: function(response) {
                    w2utils.unlock('body');
                    var text = '';
                    var title = '';
                    if(response.status == 'ok'){
                        text = JSON.stringify(response.data);
                    }else{
                        text = response.msg;
                    }
                    w2popup.open({
                        width: 750, height: 400,
                        title: 'Response',
                        body: text
                    });
                }
            });
        });

       jQuery(document).on('click', '#auo_ids_test', function(e) {
            e.preventDefault();
            w2utils.lock('body', '測試中...', true);
            jQuery.ajax({
                data: jQuery('#SettingForm').serialize(),
                type: 'POST',
                dataType: 'json',
                url: '<?php echo $this->Url->build(['controller' => 'SaasSettings', 'action' => 'auo_ids_test']); ?>',
                success: function(response) {
                    w2utils.unlock('body');
                    var text = '';
                    var title = '';
                    if(response.status == 'ok'){
                        text = response.data;
                    }else{
                        text = response.msg;
                    }
                    w2popup.open({
                        width: 750, height: 400,
                        title: 'Response',
                        body: text
                    });
                }
            });
        });
    })
    
    function add_emergency_codes(){
        w2confirm('確定為所有帳號新增各三組備用碼嗎?')
        .yes(function(){
            $j('#loading').show();
            $j('#transbtm').show();
            jQuery.ajax({
                url: 'add_emergency_codes',
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    //called when successful
                    if(data.status == 'ok'){
                        w2alert('新增成功');
                    }else{
                        w2alert('新增失敗');
                    }
                    $j('#loading').hide();
                    $j('#transbtm').hide();
                },
            });
        })
        .no(function(){
            return false;
        })
    }

</script>