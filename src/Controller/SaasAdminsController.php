<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\EventInterface;
/**
 * SaasAdmins Controller
 *
 * @property \App\Model\Table\SaasAdminsTable $SaasAdmins
 * @method \App\Model\Entity\SaasAdmin[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */

class SaasAdminsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */

    public $advancePermission;

    public function beforeFilter(EventInterface $event): void{
        parent::beforeFilter($event);
        $this->_advancePermission('SaasAdmins');
    }

    public function index()
    {
        $this->viewBuilder()->setLayout('sidebar');
        $this->loadModel('SaasSettings');
        $mfaSetting = $this->SaasSettings->find()->where(['`key`' => 'mfaSetting'])->first();
        $this->set(compact('mfaSetting'));
    }

    public function listing() {
        $this->viewBuilder()->setLayout('ajax');
        $this->do_list();
    }

    public function do_list($paginate = true){
        $w2Params = json_decode($this->request->getQuery('request'), true);
        $this->loadModel('SaasAdmins');
        $this->loadModel('SaasAuthGroups');
        $this->loadModel('SaasAdminAuthGroups');
        $auth_groups = $this->SaasAuthGroups->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])
        ->toArray();
        $authAdminGroups = $this->SaasAdminAuthGroups->find('all')
            ->group(['saas_auth_group_id', 'saas_admin_id'])
            ->toArray();
        $conditions = [];
        
        
        $findOpt = [
            'limit' => $w2Params['limit']
        ];
        if(!empty($w2Params['search'])){
            $this->request->getSession()->write('SaasAdmin.isSearch', 'true');
            $searchCons = array();
            foreach ($w2Params['search'] as $key => $value) {
                if($value['type'] == 'text'){
                    foreach((array)$value['field'] as $sfield){
                        switch ($value['operator']) {
                            case 'is':
                                $searchCons = array_merge($searchCons, array("`$sfield`" => $value['value']));
                                break;
                            case 'begins':
                                $searchCons = array_merge($searchCons, array("`$sfield` like" => "$value[value]%"));
                                break;
                            case 'contains':
                                $searchCons = array_merge($searchCons, array("`$sfield` like" => "%$value[value]%"));
                                break;
                            case 'ends':
                                $searchCons = array_merge($searchCons, array("`$sfield` like" => "%$value[value]"));
                                break;
                        }
                    }
                }

            }
            $conditions = ['or' => $searchCons];
            $this->request->getSession()->write('SaasAdmin.searchCondition', array('or' => $searchCons));
        }


        if(!empty($w2Params['sort'])){
            switch ($w2Params['sort'][0]['field']) {
                case 'name':
                    $w2Params['sort'][0]['field'] = 'SaasAdmin.name';
                    break;
                case 'username':
                    $w2Params['sort'][0]['field'] = 'SaasAdmin.username';
                    break;
                case 'last_visit':
                    $w2Params['sort'][0]['field'] = 'SaasAdmin.last_visit';
                    break;
                case 'last_visit_from':
                    $w2Params['sort'][0]['field'] = 'SaasAdmin.last_visit_from';
                    break;
                case 'active':
                    $w2Params['sort'][0]['field'] = 'SaasAdmin.active';
                        break;
            }
            $order = ['order' => [$w2Params['sort'][0]['field'] => $w2Params['sort'][0]['direction']]];
        }else{
            $order = ['order' => ['SaasAdmins.name asc']];
        }
        $findOpt = array_merge($findOpt, $order);
        if (!empty($paginate)){
            $query = $this->SaasAdmins->find()
                ->select($this->SaasAdmins)
                ->where($conditions);
            $accounts = $this->paginate($query, $findOpt);

            $records = array();
            foreach ($accounts as $k => $account) {

                $status = '';
                $bgcolor = '';
                $records[$k]['recid'] = $account->id;
                $records[$k]['name'] = $account->name;
                $records[$k]['username'] = $account->username;
                foreach ($authAdminGroups as $authAdminGroup => $value) {
                    if($account->id == $value->saas_admin_id){
                        if(empty($records[$k]['auth_groups'])){
                            $records[$k]['auth_groups'] = $auth_groups[$value->saas_auth_group_id];
                        }else{
                            $records[$k]['auth_groups'] = $records[$k]['auth_groups'] .' / '. $auth_groups[$value->saas_auth_group_id];
                        }
                    }
                }
                if(!empty($account->last_visit)){
                $records[$k]['last_visit'] = $account->last_visit->format('Y-m-d H:i:s');

                }
                $records[$k]['last_visit_from'] = $account->last_visit_from;

                if(!empty($account->active)){
                    $records[$k]['active'] = '啟用';
                    $records[$k]['w2ui']['style'] = 'background-color: #FFFFFF';

                }
                else{
                    $records[$k]['active'] = '停用';
                    $records[$k]['w2ui']['style'] = 'background-color: #E9E9E9';
                }
                if($account->is_mfa == 1){
                    $records[$k]['Mfa'] = '<span style="color:blue">啟用</span>';
                }
                else{
                    $records[$k]['Mfa'] = '停用';
                }
            }


            $results = array('total' => $this->request->getAttribute('paging')['SyncRecords']['count'], 'offset' => $w2Params['offset'], 'records' => $records);
            $this->set('jsonData', $results);
            $this->viewBuilder()->setLayout('txt');
            $this->render('/element/in_json');

        }
    }

    public function add() {
        $this->viewBuilder()->setLayout('simple');
        $this->loadModel('SaasAuthGroups');
        $this->loadModel('SaasSettings');
        $forceMfa = $this->SaasSettings->find()->where(['`key`' => 'forceMfa'])->first();
        $authGroupOpts = $this->SaasAuthGroups->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ]);
        if (!empty($this->request->getData())) {
            // $reap_message = $this->reap();
            if(!empty($reap_message)) {
                $result = $reap_message;
            } else {
                $salt = substr(sha1((string)time()), 0, 16);
                $saasAdmin = $this->SaasAdmins->newEmptyEntity();
                $saasAdmin = $this->SaasAdmins->patchEntity($saasAdmin, $this->request->getData());
                $saasAdmin->passwd = $this->othAuth->_getHashOf($saasAdmin->passwd, $salt);
                if($this->SaasAdmins->save($saasAdmin)) {
                    if(!empty($this->request->getData('group'))){
                        $this->loadModel('SaasAdminAuthGroups');
                        $saveData = array();
                        foreach ($this->request->getData('group') as $v) {
                            $saveData = $this->SaasAdminAuthGroups->newEmptyEntity();
                            $saveData->saas_admin_id = $saasAdmin->id;;
                            $saveData->saas_auth_group_id = $v;
                            $this->SaasAdminAuthGroups->save($saveData);
                        }
                    }
                    if(!empty($this->request->getData('forceMfa'))){
                        $this->loadModel('MfaBackupCodes');
                        $saveCodes = array();
                        $OptPwds = $this->_generateNumericOTP(3);
                        if(!empty($OptPwds)){
                            foreach ($OptPwds as $key => $pwd) {
                                $saveCodes = $this->MfaBackupCodes->newEmptyEntity();
                                $saveCodes->saas_admin_id = $saasAdmin->id;;
                                $saveCodes->passwd = $pwd;
                                $saveCodes->used = 0;
                                $saveCodes->creator = $this->emUid;
                                $this->MfaBackupCodes->save($saveCodes);
                            }
                        }
                    }
                    $result = 'ok';
                }else {
                    $result = 'fail';
                }
            }
            $this->set('jsonData', array('status' => $result));
            $this->layout = 'txt';
            $this->render('/element/in_json');
        }
        $this->set(compact('saasAdmin','authGroupOpts', 'forceMfa'));

    }

    public function edit($id = null) {
        $this->loadModel('SaasSettings');
        $this->viewBuilder()->setLayout('simple');
        $saasAdmin = $this->SaasAdmins->get($id, [
            'contain' => [],
        ]);
        if(!empty($this->request->getData())) {
            $orgAccountPw = $saasAdmin->passwd;
            $saasAdmin = $this->SaasAdmins->patchEntity($saasAdmin, $this->request->getData());
            $chkpwd = false;
            if($orgAccountPw == $this->request->getData('passwd')) {
                $saasAdmin->passwd = $orgAccountPw;
            } else {
                $salt = substr(sha1((string)time()), 0, 16);
                $saasAdmin->passwd = $this->othAuth->_getHashOf($this->request->getData('passwd'), $salt);
                $chkpwd = true;
            }

            $reap_message = $this->reap($id, $chkpwd);

            if(!empty($reap_message)) {
                $result = $reap_message;
            } else {
                if($this->SaasAdmins->save($saasAdmin)) {
                    if(!empty($this->request->getData('group'))){
                        $this->loadModel("SaasAdminAuthGroups");
                        $existGroups = $this->SaasAdminAuthGroups->find('list', [
                            'keyField' => 'id',
                            'valueField' => 'saas_auth_group_id'
                        ])
                        ->where(['saas_admin_id' => $id])
                        ->toArray();
                        $checkedGroups = array();
                        foreach ($this->request->getData('group') as $k => $v) {
                            if(!empty($v)){
                                $checkedGroups[] = $v;
                            }
                        }
                        $addGroups = array_diff($checkedGroups, $existGroups);

                        if(!empty($addGroups)){
                            foreach ($addGroups as $v) {
                                $saveData = $this->SaasAdminAuthGroups->newEmptyEntity();
                                $saveData['saas_admin_id'] = $id;
                                $saveData['saas_auth_group_id'] = $v;
                                $this->SaasAdminAuthGroups->save($saveData);
                            }
                        }
                        $delGroups = array_diff($existGroups, $checkedGroups);
                        if(!empty($delGroups)){
                            foreach ($delGroups as $v) {
                                $this->SaasAdminAuthGroups->deleteAll([
                                    'saas_admin_id' => $id,
                                    'saas_auth_group_id' => $v
                                ]);
                            }
                        }
                    }
                    $result = 'ok';
                }else{
                    $result = 'fail';
                }
            }
            $this->set('jsonData', array('status' => $result));
            $this->layout = 'txt';
            $this->render('/element/in_json');
        } else {
            $this->loadModel('SaasAuthGroups');
            $authGroupOpts = $this->SaasAuthGroups->find('list', [
                'keyField' => 'id',
                'valueField' => 'name'
            ])
            ->toArray();

            $authGroupOpts = $this->SaasAuthGroups->find('list', [
                'keyField' => 'id',
                'valueField' => 'name'
            ])
            ->toArray();

            $this->loadModel("SaasAdminAuthGroups");
            $saasAdmin->group = $this->SaasAdminAuthGroups->find('list', [
                'keyField' => 'id',
                'valueField' => 'saas_auth_group_id'
            ])
            ->where(['saas_admin_id' => $id])
            ->toArray();

            $this->loadModel('MfaBackupCodes');
            $BackupCodes = $this->MfaBackupCodes->find()
            ->select(['id', 'passwd','used'])
            ->where(['saas_admin_id' => $id])
            ->toArray();

            if(empty($saasAdmin->sys_auth)){
                $saasAdmin->auth_limit = 0;
            }else{
                $sys_auth = unserialize($saasAdmin->sys_auth);
                $saasAdmin->auth_limit = $sys_auth['auth_limit'];
                $saasAdmin->maintain_month = $sys_auth['maintain_month'];
            }
            if(!empty($BackupCodes)){
                foreach ($BackupCodes as $key => $code) {
                    if(!empty($code->passwd)){
                        $BackupCodes[$key]->passwd = $this->_passport_decrypt($code->passwd);
                    }
                }
                $this->set('BackupCodes', $BackupCodes);
            }
            $this->set(compact('saasAdmin','authGroupOpts'));
        }

    }

    public function delete($id = false){
        if(!empty($id)){
            if($id != $this->emUid){
                $this->SaasAdmins->deleteAll(['id IN' => $id]);
                $status = 'ok';
            }else{
                $status = '不能刪除正在使用的帳號';
            }
        }else{
            $status = 'id 不存在';
        }
        $results = array('status' => $status);
        $this->set('jsonData', $results);
        $this->viewBuilder()->setLayout('txt');
        $this->render('/element/in_json');
    }

    public function getMfa() {
        $this->viewBuilder()->setLayout('ajax_simple');
        $adminInfo = unserialize($this->request->getSession()->read('EmmaApp.UserInfo'));
        $mfaStatus = 'not_open';
        $imgStr = '';
        $isMfa = 0;
        require_once ROOT . DS . 'vendor' . DS . 'GoogleAuthenticator' . DS . 'PHPGangsta' . DS . 'GoogleAuthenticator.php';
        $ga = new \PHPGangsta_GoogleAuthenticator();

        $accountData = $this->SaasAdmins->find()
        ->where(['SaasAdmins.id' => $adminInfo['Uid']])
        ->first();

        $isMfa = $accountData['is_mfa'];
        if(!empty($accountData['mfa_key'])) {
            $mfaStatus = 'have_mfa';
            $qrCodeUrl = $ga->getQRCodeGoogleUrl('FemasHr', $accountData['SaasAdmin']['mfa_key'], null, array('width'=>'100px', 'height'=>'100px'));
            $imgStr = '<img src="'.$qrCodeUrl.'">';
        } else {
            $mfaStatus = 'not_gen';
        }
        $this->set('jsonData', compact(array('mfaStatus', 'imgStr', 'isMfa')));
        $this->render('/element/in_json');
    }

    public function editMyAccount() {
        $this->viewBuilder()->setLayout('sidebar');
        $saasAdmin = $this->SaasAdmins->get($this->emUid, [
            'contain' => [],
        ]);
        if(!empty($this->request->getData())) {
            $orgAccountPw = $saasAdmin->passwd;
            // $this->log(var_export($orgAccountPw, true));
            // $this->log(var_export($this->othAuth->_passwdCompare($this->request->getData('orig_passwd')), true));
            if(!empty($this->request->getData('orig_passwd')) || !empty($this->request->getData('new_passwd')) || !empty($this->request->getData('confirm_new_passwd'))){
                if(!$this->othAuth->_passwdCompare($this->request->getData('orig_passwd'), $orgAccountPw)){
                    $result = 'fail';
                    $msg = __('原始密碼不正確，更新失敗', true);
                }else{
                    $salt = substr(sha1((string)time()), 0, 16);
                    $newPass = $this->request->getData('new_passwd');
                    $saasAdmin['passwd'] = $this->othAuth->_getHashOf($newPass, $salt);
                    if($this->SaasAdmins->save($saasAdmin)){
                        $result = 'success';
                        $msg = __('已更新密碼', true);
                    }
                }
            }

            $this->set('jsonData', array('status' => $result, 'msg' => $msg));
            $this->viewBuilder()->setLayout('txt');
            $this->render('/element/in_json');
        } else {
            $this->loadModel('SaasAuthGroups');
            $this->loadModel('SaasAdminAuthGroups');
            $authGroupOpts = $this->SaasAuthGroups->find('list', [
                'keyField' => 'id',
                'valueField' => 'name'
            ])
            ->toArray();
            $saasAdmin['group'] = $this->SaasAdminAuthGroups->find('list', [
                'keyField' => 'id',
                'valueField' => 'saas_auth_group_id'
            ])
            ->where(['saas_admin_id' => $this->emUid])
            ->toArray();

            $this->set(compact('saasAdmin', 'authGroupOpts'));
        }

    }

    public function reap($id = null, $chkpwd = false) {
        if(!empty($id)) {
            $conditions = ['id !=' => $id, 'username' => $this->request->getData('username')];
        } else {
            $conditions = ['username' => $this->request->getData('username')];
        }
        $DuplicateCount = $this->SaasAdmins->find()
        ->where($conditions)
        ->count();
        $reap_message = '';
        if(empty($this->request->getData('username'))) {
            $reap_message = __('帳號請勿空白', true);
        } elseif(empty($this->request->getData('name'))){
            $reap_message = __('姓名請勿空白', true);
        } elseif(empty($this->request->getData('passwd'))){
            $reap_message = __('密碼請勿空白', true);
        } elseif(empty($this->request->getData('group'))) {
            $reap_message = __('請設定權限群組', true);
        }elseif($DuplicateCount > 0) {
            $reap_message = __('請檢查是否有重複帳號', true);
        }

        if(empty($reap_message) && !empty($chkpwd)){
            $reap_message = $this->validatePassword($this->request->getData('passwd'));
        }
        return $reap_message;
    }

    public function validatePassword($password) {
        $err = '';
        if (strlen($password) < 12) {
            $err = '密碼必須至少 12 個字元。';
        }
        $hasUpper = preg_match('/[A-Z]/', $password);
        $hasLower = preg_match('/[a-z]/', $password);
        $hasNumber = preg_match('/[0-9]/', $password);
        $hasSpecial = preg_match('/[@$%^&*()~!]/', $password);
        $typesCount = $hasUpper + $hasLower + $hasNumber + $hasSpecial;
        if ($typesCount < 3) {
            $err = '密碼必須包含至少三種以下四種符號：大寫字母、小寫字母、數字、特殊符號。';
        }
        return $err;
    }

    public function login(){
        $this->viewBuilder()->setLayout('login'); // 替換為你的佈局名稱
        $this->loadModel('SaasSettings');

        $mfaSetting = $this->SaasSettings->find()->where(['`key`' => 'mfaSetting'])->first();
        $forceMfa = $this->SaasSettings->find()->where(['`key`' => 'forceMfa'])->first();
        $max = '';
        if($this->request->getParam('url.max')){
            $max = (int)$this->request->getParam('url.max');
        }
        if(!empty($this->request->getData())) {
            $isMfa = $this->checkMfa(true);
            $isMfaPass = $this->checkMfaCode(true);
            if(empty($this->request->getData('SaasAdmin.username')) or empty($this->request->getData('SaasAdmin.passwd'))) {
                $this->Flash->error(__('帳號/密碼或兩階段驗證碼 錯誤', true));
            } else {
                // $this->log(var_export($this->request->getData(), true));
                $authError = $this->othAuth->login($this->request->getData('SaasAdmin'));
                $this->Flash->error($this->othAuth->getMsg($authError));
            }
        }else{
            if($this->request->getSession()->check('Accounts.logout')) {
                if ($this->request->getSession()->check('Accounts.logoutMsg')) {
                    $this->Flash($this->request->getSession()->read('Accounts.logoutMsg'));
                    $this->request->getSession()->delete('Accounts.logoutMsg');
                }elseif ($this->request->getSession()->read('Accounts.timeout') == true) {
                    $this->Flash->sussess(__('操作閒置，自動登出系統！', true));
                } else {
                    $this->Flash->sussess(__('您已經成功登出系統！', true));
                }
            }
            $this->request->getSession()->destroy();
        }

        $this->set(compact('max','mfaSetting', 'forceMfa'));
    }

    public function logout($timeout = false) {
        $this->viewBuilder()->setLayout('login');
        
        $this->othAuth->logout();
        $this->request->getSession()->write('Accounts.logout', '1');
        $this->request->getSession()->write('Accounts.timeout', $timeout);
        $this->redirect(array('action'=>'login'));
    }

    function genMfa() {
        $this->viewBuilder()->setLayout('ajax_simple');
        require_once ROOT . DS . 'vendor' . DS . 'GoogleAuthenticator' . DS . 'PHPGangsta' . DS . 'GoogleAuthenticator.php';
        $ga = new \PHPGangsta_GoogleAuthenticator();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl('Femas_mw-auo', $secret, 'fonsen femas hr', array('width'=>'100px', 'height'=>'100px'));
        $imgStr = '<img src="'.$qrCodeUrl.'">';

        $hash = hash('SHA384', (string)$this->hashStr, true);
        $key = substr($hash, 0, 32);
        $iv = substr($hash, 32, 16);
        $padding = 16 - (strlen($secret) % 16);
        $secret .= str_repeat(chr($padding), $padding);
        $encrypt = openssl_encrypt($secret, 'AES-128-CBC', $key, 0, $iv);
        $secret = base64_encode($encrypt);

        $this->set('jsonData', compact(array('imgStr', 'secret')));
        $this->render('/element/in_json');
    }

    public function checkMfa($mfaCheck = false) {
        $this->loadModel('SaasSettings');
        $forceMfa = $this->SaasSettings->find()->where(['`key`' => 'forceMfa'])->first();
        $conditions = ['username' => $this->request->getData('SaasAdmin.username')];
        $accountData = $this->SaasAdmins->find()->where([$conditions])->first();
        $isMfa = $accountData->is_mfa;

        if(empty($isMfa) && !empty($forceMfa->value)){
            $this->loadModel('MfaBackupCodes');
            $adminId = $accountData->id;
            $BackupCodes = $this->MfaBackupCodes->find('list', array('fields' => array('id', 'passwd'), 'conditions' => array('saas_admin_id' => $adminId, 'used' => 0)));
            if (empty($accountData)) {
                $isMfa = null;
            }else if(empty($BackupCodes)){
                $isMfa = -1;
            }else{
                $isMfa = $forceMfa->value;
            }
        }
        if(!empty($mfaCheck)){
            return $isMfa;
        }else{
            $this->viewBuilder()->setLayout('ajax_simple');
            $this->set('jsonData', compact(array('isMfa')));
            $this->render('/element/in_json');
        }
    }

    public function checkMfaCode($mfaCheck = false) {
        if($this->request->getSession()->read('EmmaApp.UserInfo')){
            $adminInfo = unserialize($this->request->getSession()->read('EmmaApp.UserInfo'));
        }
        $this->loadModel('SaasSettings');
        $mfaKey = '';
        $isMfaPass = 0;
        if(!empty($this->request->getData('data.mfaKey'))) {
            $hash = hash('SHA384', (string)$this->hashStr, true);
            $key = substr($hash, 0, 32);
            $iv = substr($hash, 32, 16);
            $data = openssl_decrypt(
                base64_decode($this->request->getData('data.mfaKey')), 
                'AES-128-CBC', 
                $key, 
                0, 
                $iv
            );
            $padding = ord($data[strlen($data) - 1]);
            $mfaKey = substr($data, 0, -$padding);
        } else {
            $conditions = ['username' => $this->request->getData('SaasAdmin.username')];
            $accountData = $this->SaasAdmins->find()->where([$conditions])->first();
            $mfaKey = $accountData->mfa_key;
            $forceMfa = $this->SaasSettings->find()->where(['`key`' => 'forceMfa'])->first();
            $mfaKey = !empty($mfaKey)? $mfaKey : $this->request->getData('data.mfaCode');
        }

        require_once ROOT . DS . 'vendor' . DS . 'GoogleAuthenticator' . DS . 'PHPGangsta' . DS . 'GoogleAuthenticator.php';
        $ga = new \PHPGangsta_GoogleAuthenticator();

        if(!empty($mfaKey) && !empty($this->request->getData('data.mfaCode'))) {
            $checkResult = $ga->verifyCode($mfaKey, $this->request->getData('data.mfaCode'), 1);
            if($checkResult) {
                $isMfaPass = 1;
                if(!empty($this->request->getData('data.type'))&&$this->request->getData('data.type')=='save') {
                    $saasAdmin = $this->SaasAdmins->get($this->emUid, [
                        'contain' => [],
                    ]);
                    $saasAdmin['mfa_key'] = $mfaKey;
                    $saasAdmin['is_mfa'] = 1;
                    $this->SaasAdmins->save($saasAdmin);
                }
            } else {
                if(!empty($accountData) && !empty($forceMfa['value'])){
                    $this->loadModel('MfaBackupCodes');
                    $adminId = $accountData['id'];

                    $BackupCodes = $this->MfaBackupCodes->find('list', [
                        'keyField' => 'id',
                        'valueField' => 'passwd'
                    ])
                    ->where(['saas_admin_id' => $adminId, 'used' => 0])
                    ->toArray();

                    if(!empty($BackupCodes)){
                        foreach ($BackupCodes as $id => $code) {
                            if($this->request->getData('data.mfaCode') == $this->_passport_decrypt($code)){
                                $isMfaPass = 1;
                                $this->request->getSession()->write('SaasAdmin.BackupCodeId', $id);
                            }

                        }
                    }
                }
            }
        }
        if(!empty($mfaCheck)){
            return $isMfaPass;
        }else{
            $this->viewBuilder()->setLayout('txt');
            $this->set('jsonData', compact(array('isMfaPass')));
            $this->render('/element/in_json');
        }
    }

    public function mfasetting(){
        $this->viewBuilder()->setLayout('ajax_sdiv');
        $this->loadModel('SaasSettings');
        $adminInfo = unserialize($this->request->getSession()->read('EmmaApp.UserInfo'));
        if(!empty($this->request->getData())){

            $adminInfo = unserialize($this->request->getSession()->read('EmmaApp.UserInfo'));
            $saasAdmin = $this->SaasAdmins->get($adminInfo['Uid'], [
                'contain' => [],
            ]);
            $saasAdmin['is_mfa'] = $this->request->getData('data.isMfa');
            if($this->SaasAdmins->save($saasAdmin)){
                $result = 'ok';
            }else{
                $result = 'err';
            }
        }

        $this->set('jsonData', compact('result'));
        $this->layout = 'txt';
        $this->render('/element/in_json');
    }

    function clearMfa(){
        $this->layout = 'ajax';
        $adminInfo = unserialize($this->request->getSession()->read('EmmaApp.UserInfo'));
        $saasAdmin = $this->SaasAdmins->get($adminInfo['Uid'], [
            'contain' => [],
        ]);
        $saasAdmin['is_mfa'] = 0;
        $saasAdmin['mfa_key'] = '';

        if($this->SaasAdmins->save($saasAdmin)){
            $result = 'ok';
        }else{
            $result = 'err';
        }
        $this->set('jsonData', compact('result'));
        $this->layout = 'txt';
        $this->render('/element/in_json');
    }

    public function addEmergencyCodes($id){
        $this->loadModel('SaasAdmins');
        $this->loadModel('MfaBackupCodes');
        $this->loadModel('SaasSettings');

        $SaasAdmins = $this->SaasAdmins->find('list', [
            'keyField' => 'id',
            'valueField' => 'id'
        ])
        ->where(['id' => $id, 'active' => 1])
        ->toArray();
        $this->MfaBackupCodes->deleteAll(['saas_admin_id IN' => $SaasAdmins]);
        $saveDatas = array();
        $i = 0;
        foreach ($SaasAdmins as $id) {
            $OptPwds = $this->_generateNumericOTP(3,6);
            if(!empty($OptPwds)){
                foreach ($OptPwds as $key => $pwd) {
                    $saveDatas[$i]['saas_admin_id'] = $id;
                    $saveDatas[$i]['passwd'] = $pwd;
                    $saveDatas[$i]['used'] = 0;
                    $saveDatas[$i]['creator'] = $this->emUid;
                    $i ++;
                }
            }
        }
        $BackupCodes = $this->MfaBackupCodes->newEntities($saveDatas);
        if($this->MfaBackupCodes->saveMany($BackupCodes, ['atomic' => false])){
            $result = 'ok';
            $newCodes = array();
            foreach ($saveDatas as $key => $data) {
                $newCodes[] = $this->_passport_decrypt($data['passwd']);
            }
        }else{
            $result = 'fail';
        }

        $this->set('jsonData', array('status' => $result, 'new' => $newCodes));
        $this->layout = 'txt';
        $this->render('/element/in_json');

    }

}
