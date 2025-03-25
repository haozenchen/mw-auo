<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;
use Cake\Controller\Controller;
use Cake\Cache\Cache;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */

    /**
     * authentication restrictions on action
     *  options: string or array(ex: 'add', 'view')
     */
    var $othAuthRestrictions = "*";
    /**
     * emma user id, not account id
     */
    var $emUid = null;
    /**
     * emma department id
     */
    var $emDptid = null;
    /**
     * Emma's function and description
     */
    var $emmaFuncs = null;
    var $ajaxBoxOff = false;
    /**
     * Emma's user name
     */
    var $emUserName = '使用者';
    /**
     * Emma's department name
     */
    var $emDptName = '';
    /**
     * Emma's account id
     */
    var $emAccId = null;
    var $emDate = array();
    var $noCheckAction = array();
    var $initialized = false;
    /**
     * if set to true, del session var 'Config'
     * created for flash upload
     */
    var $delSessionConfig = false;
    var $loginValid = false;
    /**
     * list mode, used in listing/paginating scenes
     * valid are paginate/find, default is paginate
     * find is mostly used in spreadsheet download
     */
    var $listMode = 'paginate';
    var $setNoCache = true;
    var $crmSecretKey = 'oJStyzLmpQWcOVnA';



    public function initialize(): void
    {   
        Cache::clear();

        parent::initialize();

        $this->loadComponent('othAuth');
        $this->loadComponent('ComLoader');
        $this->loadComponent('Cookie');
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        $this->viewBuilder()->setHelpers(['Html', 'OthAuth', 'Session']);
        $this->advPermissions = [];
        $this->advancePermission = [];
        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }

    public  function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->topMenus = array(
            '0' => array('name' => '同步執行作業', 'icon' => 'fas fa-sync','display' => 1,  'link' => 'SyncRecords/index'),
            '1' => array('name' => '參數設定', 'icon' => 'fas fa-quote-right','display' => 1,  'link' => 'SaasSettings/saas_settings_index'),
            '2' => array('name' => '帳號管理', 'icon' => 'fas fa-users', 'link' => 'SaasAdmins/index'),
            '3' => array('name' => '權限群組管理', 'icon' => 'fas fa-user-lock', 'link' => 'SaasAuthGroups/index'),
            '4' => array('name' => __('我的帳號', true), 'icon' => 'fas fa-user-circle', 'link' => 'SaasAdmins/edit_my_account')
        );

        $this->advPermissionCategories = array('add', 'edit', 'delete');

        // subMenu Permission
        $this->advPermissions[0] = array(
            'add' => array('title'=>'新增', 'policyName'=> 'SyncRecords.add', 'inputType' => 'checkbox', 'default' => '0')
        );
        // 系統管理
        $this->advPermissions[1] = array(
             'edit' => array('title' => '修改', 'policyName' => 'SaasSettings.edit', 'inputType' => 'checkbox', 'default' => '0'),
        );
        $this->advPermissions[2] = array(
            'add' => array('title'=>'新增', 'policyName'=> 'SaasAdmins.add', 'inputType' => 'checkbox', 'default' => '0'),
            'edit' => array('title' => '修改', 'policyName' => 'SaasAdmins.edit', 'inputType' => 'checkbox', 'default' => '0'),
            'delete' => array('title' => '刪除', 'policyName' => 'SaasAdmins.delete', 'inputType' => 'checkbox', 'default' => '0'),
        );

        $this->advPermissions[3] = array(
            'add' => array('title'=>'新增', 'policyName'=> 'SaasAuthGroups.add', 'inputType' => 'checkbox', 'default' => '0'),
            'edit' => array('title' => '修改', 'policyName' => 'SaasAuthGroups.edit', 'inputType' => 'checkbox', 'default' => '0'),
            'delete' => array('title' => '刪除', 'policyName' => 'SaasAuthGroups.delete', 'inputType' => 'checkbox', 'default' => '0'),
        );

        $this->langType = 'zh-tw';
    }

    function beforeRender(\Cake\Event\EventInterface $event) {
        if($this->setNoCache) {
            header ("Cache-Control: no-store, no-cache, must-revalidate");
        }
        $this->_readUserInfo();
        $this->emDate = getdate();
        $this->set('emma_user_name', $this->emUserName);
        $this->set('emma_date', $this->emDate);

        $langType = $this->langType;
        $topMenus = array();
        $menuItems = array();
        $menuBlockKey = '';
        $menuItemKey = '';
        $langType = '';
        if($this->request->getSession()->read('EmmaApp.AuthMenus')){
            $topMenus = unserialize($this->request->getSession()->read('EmmaApp.AuthMenus'));
        }
        if($this->request->getSession()->read('EmmaApp.AuthSubMenus')){
            $menuItems = unserialize($this->request->getSession()->read('EmmaApp.AuthSubMenus'));
        }
        if(empty($topMenus)){
            list($topMenus, $menuItems) = $this->__permissionMenus();
        }
        if(!empty($menuItems)){
            $tmpAction = $this->params['controller'] . '/' . $this->action;
            foreach($menuItems as $menuItem){

                foreach($menuItem['MenuItem'] as $k => $v){
                    if($v['action'] == $tmpAction){
                        $menuBlockKey = $menuItem['id'];
                        $menuItemKey = $k;
                        break 2;
                    }
                }
            }
        }

        if($this->request->getSession()->check('AppController.mfaAlert')){
            $mfaAlert = '請盡速啟用MFA兩階段認證!以免日後無法登入!';
            $this->set('mfaAlert', $mfaAlert);
            $this->request->getSession()->delete('AppController.mfaAlert');
        }

        $this->set('mod_menu', $menuItems);
        $this->set('top_menuData', $topMenus);
        $this->set(compact('menuBlockKey', 'menuItemKey', 'langType'));
        // parent::beforeRender();
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        $params = $this->request->getParam('pass');
        foreach ($params as $param) {
            if (!preg_match('/^[0-9a-zA-Z\-\_]+$/', $param)) {
                return new Response(['body' => 'Invalid parameter format', 'status' => 400]);
            }
        }


       $auth_conf = array(
            'mode'  => 'oth',
            'login_page'  => EMMA_LOGIN,
            'logout_page' => EMMA_LOGOUT,
            'hashkey'     => 'oJStyzLmpQWcOVnA',
            'strict_gid_check' => false
        );

        $this->login_page = $auth_conf['login_page'];
        $this->loginVal = $this->request->getSession()->read('othAuth.'.$auth_conf['hashkey']);

        $this->loadModel('SaasSettings');
        $lifeTime = $this->SaasSettings->find()->where(['`key`' => 'LoginLifeTime'])->first();
        $loginLifeTime = $lifeTime->value;
        if(empty($loginLifeTime)) {
            $loginLifeTime = 1440; //延遲多少秒 登出
        }
        $this->set('sessionMaxLifeTime', $loginLifeTime);
        $this->noCheckAction = array('login', 'logout', 'checkMfa','checkMfaCode','chgPwd');//actions need not login
        $this->login_page = $auth_conf['login_page'];
        $this->loginVal = $this->request->getSession()->read('othAuth.'.$auth_conf['hashkey']);
        if($this->request->getSession()->read('EmmaApp.UserInfo')){
            $this->userInfo = unserialize($this->request->getSession()->read('EmmaApp.UserInfo'));
        }
        if(!empty($this->userInfo)){
            $this->emUid = $this->userInfo['Uid'];
            $this->emUserName = $this->userInfo['UserName'];
            $this->forceMfa = $this->userInfo['forceMfa'];
        }elseif(!empty($this->loginVal)){
                $this->userInfo = array(
                'Uid' => $this->loginVal['id'],
                'UserName' => $this->loginVal['name'],
                'forceMfa' => $this->loginVal['force_mfa'],
                'permissionAction' => $this->loginVal['permissionAction'],
                'advancePermission' => $this->loginVal['advancePermission'],
            );
            $this->request->getSession()->write('EmmaApp.UserInfo', serialize($this->userInfo));
            $this->emUid = $this->loginVal['id'];
            $this->emUserName = $this->loginVal['name'];
            $this->forceMfa = $this->loginVal['force_mfa'];
        }else{
            if (!in_array($this->request->getParam('action'), $this->noCheckAction)) {
                $this->redirect($this->login_page, 302);
            }
        }
        // $auth = $this->__authRefererPermissions();
        // if(!$auth){
        //     header("HTTP/1.1 401 Unauthorized");
        //     exit;
        // }
    }

    public function _readUserInfo() {
        if ($this->request->getSession()->check('EmmaApp.UserInfo')) {
            $data = unserialize($this->request->getSession()->read('EmmaApp.UserInfo'));

            $this->emUid = $data['Uid'];
            $this->emUserName = $data['UserName'];
            $this->permissionAction = $data['permissionAction'];
            $this->advancePermission = $data['advancePermission'];
        }
    }

    public function __permissionMenus(){
        $allActions = array();
        $menus = array();
        $subMenus = array();
        if($this->permissionAction == 'allow_all_action'){
            $menus = $this->topMenus;
            $subMenus = $this->menuItems;
        } else {
            $tmpMenus = $this->topMenus;
            $tmpSubMenus = $this->menuItems;
            foreach ($tmpMenus as $k => $menu) {
                $allActions[$k] = $menu['link'];
                if(!empty($tmpSubMenus[$k])){
                    foreach ($tmpSubMenus[$k] as $k2 => $subMenu) {
                        $allActions[$k.'_'.$k2] = $subMenu['action'];
                    }
                }
                if(!empty($this->permissionAction)){
                    if(!in_array($menu['link'], $this->permissionAction)){
                        unset($tmpMenus[$k]);
                        continue;
                    }
                }else{
                    unset($tmpMenus[$k]);
                    continue;
                }
                
                if(!empty($tmpSubMenus[$k])){
                    foreach ($tmpSubMenus[$k] as $k2 => $subMenu) {
                        if(!in_array($subMenu['action'], $this->permissionAction) ){
                            unset($tmpSubMenus[$k][$k2]);
                            continue;
                        }
                    }
                    sort($tmpSubMenus[$k]);
                }
            }
            $menus = $tmpMenus;
            $subMenus = $tmpSubMenus;
        }
        if(!empty($subMenus)){
            foreach ($subMenus as $k => $v) {
                foreach ($v as $k2 => $v2) {
                    $subMenus[$k][$k2] = $v2;
                    $subMenus[$k][$k2]['id'] = $k2;
                }
            }        
        }
        $this->request->getSession()->write('EmmaApp.AuthMenus', serialize($menus));
        $this->request->getSession()->write('EmmaApp.AuthSubMenus', serialize($subMenus));
        $this->request->getSession()->write('EmmaApp.allActions', serialize($allActions));
        return array($menus, $subMenus);
    }

    public function _advancePermission($model = null){
        if($this->userInfo['advancePermission'] == 'grant_all'){
            $this->$model->advancePermission = $this->userInfo['advancePermission'];
        }else{
            $this->$model->advancePermission = $this->userInfo['advancePermission'][$model];
        }
        $this->set('advancePermission', $this->$model->advancePermission);
    }

    public function __authRefererPermissions(){
        $auth = false;
        
        if(!in_array($this->request->getParam('action'), $this->noCheckAction)){
            $appPath = MWROOT;
            if(empty($_SERVER['HTTP_REFERER'])){
                return $auth;
            }
            $referer = str_replace(array('http://', 'https://'), '', $_SERVER['HTTP_REFERER']);
            if($this->userInfo['permissionAction'] == 'allow_all_action'){
                $auth = true;
            }else{
                foreach ($this->userInfo['permissionAction'] as $v) {
                    $compareStr = $_SERVER['HTTP_HOST'].'/'.$appPath.'/'.$v;
                    // $this->log(var_export($compareStr, true));
                    // $this->log(var_export($compareStr, true));
                    if($referer === $compareStr || strpos($referer, $compareStr)!==false){
                        $auth = true;
                        break;
                    }
                }
                if(!$auth){
                    $compareStr = [$_SERVER['HTTP_HOST'].'/'.$appPath.'/saas-admins/login', $_SERVER['HTTP_HOST'].'/'.$appPath.'/SaasAdmins/login'];
                    foreach ($compareStr as $str) {
                       if($referer === $str || strpos($referer, $str)!==false){
                            $auth = true;
                            break;
                        }
                    }
                    
                }
            }
            if($this->request->getSession()->read('EmmaApp.allActions')){
                $allActions = unserialize($this->request->getSession()->read('EmmaApp.allActions'));
            }else{
                $allActions = array();
            }
            
            if(in_array($this->request->getRequestTarget(), $allActions) && !in_array($this->request->getRequestTarget(), $this->userInfo['permissionAction']) && $this->userInfo['permissionAction'] != 'allow_all_action'){
                $url = (stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/'.$appPath.'/'.current($this->userInfo['permissionAction']);
                header("Location: ".$url);
                exit();
            }
        }else{
            $auth = true;
        }
        return $auth;
    }

    public function _generateNumericOTP($accountNum = null ,$n = 6) {
        $generator = "0123456789";
        $secret = $this->crmSecretKey;
        $OptPwds = array();
        for($k=0; $k < $accountNum; $k++){
            $result = "";
            for ($i = 1; $i <= $n; $i++) {
                $result .= substr($generator, (rand()%(strlen($generator))), 1);
            }

            $result = $this->_passport_encrypt($result, $secret);
            $OptPwds[$k] = $result;
        }
        return $OptPwds;
    }
    public function _passport_encrypt($pwd, $key = null) {
        $encrypt_key = md5((string)rand(0, 32000));
        $ctr = 0;
        $tmp = '';
        for($i = 0;$i < strlen($pwd); $i++) {
        $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
        $tmp .= $encrypt_key[$ctr].($pwd[$i] ^ $encrypt_key[$ctr++]);
        }
        return urlencode(base64_encode($this->_passport_key($tmp, $key)));
    }

    public function _passport_decrypt($pwd, $key = null) {
        if(empty($key)){
            $key =$this->crmSecretKey;
        }
        $pwd = $this->_passport_key(base64_decode(urldecode($pwd)), $key);
        $tmp = '';
        for($i = 0;$i < strlen($pwd); $i++) {
        $md5 = $pwd[$i];
        $tmp .= $pwd[++$i] ^ $md5;
        }
        return $tmp;
    }

    public function _passport_key($pwd, $encrypt_key) {
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $tmp = '';
        for($i = 0; $i < strlen($pwd); $i++) {
        $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
        $tmp .= $pwd[$i] ^ $encrypt_key[$ctr++];
        }
        return $tmp;
    }
}