<?php
/*
 * othAuth an auth system for cakePHP
 * comments, bug reports are welcome crazylegs AT gmail DOT com
 * @author Othman Ouahbi aka CraZyLeGs
 * @website: http://www.devmoz.com/blog/
 * @version 0.5.4.5
 * @license MIT
 * todo Router::url() in cakeAdmin and probably somewhere else
 */
/**
 * $Id: oth_auth.php,v 1.10 2022/10/28 08:32:25 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/10/28 08:32:25 $
 */
/**
 * original author: Othman Ouahbi
 * now modified by Fonsen Technology, all rights reserved
 *
 * @copyright  Copyright 2007, Fonsen Technology Ltd. Corp.
 */
/**
 * @deprecated CAKE_ADMIN_AUTH_ONLY should be removed soon
 */
namespace App\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Controller\ComponentRegistry;
use Cake\Utility\Inflector;
define('CAKE_ADMIN_AUTH_ONLY', Configure::read('Routing.admin'));

class OthAuthComponent extends Component
{

/**
* Constants to modify the behaviour of othAuth Component
*/
	// Form vars
	var $user_login_var        = 'username';
	var $user_passw_var        = 'passwd';
	var $user_group_var        = 'pgroup_id';
	var $user_cookie_var       = 'cookie';

	// DB vars
	var $user_table       	   = array('SaasAdmins');

	var $user_table_login      = 'username';
	var $user_table_passw      = 'passwd';
	var $user_table_gid        = 'pgroup_id';
	var $user_table_active     = 'active';
	var $user_table_last_visit = 'last_visit';
	var $auth_url_redirect_var = 'from';
	var $show_auth_url_redirect_var = true; // decorate the url or not
	var $user_model       = 'SaasAdmin';
	var $group_model      = 'Pgroup';
	var $permission_model = 'Permission';

	var $history_active   = false;
	var $history_model    = 'UserHistory';
	/*
	 * Internals you don't normally need to edit those
	 */
	var $components    = array('RequestHandler');
	var $controller    = true;
	var $gid = 1;
	var $redirect_page;
	var $hashkey       = "oJStyzLmpQWcOVnA";
	var $auto_redirect = true;

	var $login_page    = EMMA_LOGIN;
	var $logout_page   = EMMA_LOGOUT;
	var $access_page   = EMMA_USER_ACCESS;
	var $noaccess_page = EMMA_NON_ACCESS; // session_flash, flash, back or a page url

	var $mode = 'oth';
	var $pass_crypt_method   = 'sha256'; // md5, sha1, sha256, crypt, crc32,callback
	var $pass_crypt_callback = null; // function name
	var $pass_crypt_callback_file = ''; // file where the function is declared ( in vendors )


	var $cookie_active    = true;
	var $cookie_lifetime = '+1 day';

	// asc : the most important group is the group with smallest value
	// desc: the most important group is the group with greatest value
	var $gid_order = 'asc'; // asc desc
	var $strict_gid_check = true;

	var $kill_old_login = true; // when true, form can have another login with the same hash and del the old

	var $allowedAssocUserModels       = array();
	var $allowedAssocGroupModels      = array();
	var $allowedAssocPermissionModels = array();

	var $allowedLoginChars = array('@','.','_', '-');

	var $error_number = 0;


	var $login_limit = false; // flag to toggle login attempts feature

	var $login_attempts_model = 'LoginAttempts';


	var $login_attempts_num = 3;

	var $login_attempts_timeout = EMMA_LOGIN_TIMEOUT; // in minutes

	var $login_locked_out = '+1 day';


	public function initialize(array $config): void
    {	
    	date_default_timezone_set('Asia/Taipei');
		// pass auth data to the view so it can be used by the helper
		$this->_passAuthData();
        parent::initialize($config);

        // 其他初始化代碼
    }

	// startup() is kindof useless here because we init the component in beforeFilter,
	// and startup is called after that and before the action.
	// $this->othAuth->controller = $this;
    function startup(&$controller)
    {
       $this->controller = $controller;
    }

    function _getGidOp()
    {
    	if($this->strict_gid_check)
    	{
    		return '';
    	}else
    	{
    		return ($this->gid_order == 'desc')? '>=' : '<=';
    	}
    }

	function _getHashOf($str, $salt = ''){
		switch($this->pass_crypt_method)
		{
			case 'sha1':
				$str .= !empty($salt)?$salt:'';
				return $salt.($str == '')? '' : sha1($str).(!empty($salt)?'salt':'');
			break;
			case 'sha256':
				$str .= !empty($salt)?$salt:'';
				return $salt.(($str == '')?'':hash('sha256', $str)).(!empty($salt)?'salt':'');
			break;
			case 'crypt':
				return $salt.crypt($str, $salt).(!empty($salt)?'salt':'');
			break;
			case 'callback':
				App::import('vendor', $this->pass_crypt_callback_file);
				if(function_exists($this->pass_crypt_callback))
				{
					return call_user_func($this->pass_crypt_callback,$str);
				}
				return false;
			break;
			case 'md5':
			default:
				$str .= !empty($salt)?$salt:'';
				return $salt.md5($str).(!empty($salt)?'salt':'');
			break;
		}
	}

	function login($ap = 1,$order ='asc') // username,password,group
	{

		$controller = $this->_registry->getController();

        // 獲取 request 中的 data
        $data = $controller->getRequest()->getData();
		if(!$this->_checkLoginAttempts())
		{
			return -3; // too many login attempts
		}

		$params = null;
		if(!empty($data[$this->user_model]))
		{
			$params[$this->user_model] = $data[$this->user_model];
		}
		return $this->_login($params);


	}

	function _login($params,$ignore_cookie = false)
  {
    switch ($this->mode)
		{
		case 'oth':
			return $this->othLogin($params,$ignore_cookie);
			break;
		case 'nao':
			return $this->naoLogin($params,$ignore_cookie);
			break;
		case 'acl':
			return $this->aclLogin($params,$ignore_cookie);
			break;
		default:
			return $this->othLogin($params,$ignore_cookie);
			break;
		}
	}

	/**
	 * build ldap connection
	 * can try several host (if given in comma separated, like 127.0.0.1,192.168.1.1)
	 * @param array $ldapConf
	 * @param string $ldapUser
	 * @param string $ldapPsw
	 */
   	function _ldapConn($ldapConf, $ldapUser, $ldapPsw) {
		$ldapConnTimeout = ife($ldapConf['LdapConnTimeout'], $ldapConf['LdapConnTimeout'], 8);
		if (empty($ldapConf['LdapHost'])) {
			return false;
		}
		if (strpos($ldapConf['LdapHost'], ',')) {
			$hosts = split(',', $ldapConf['LdapHost']);
		} else {
			/* single */
			$hosts = array($ldapConf['LdapHost']);
		}
		foreach ($hosts as $host) {
			if (@fsockopen($host, 389, $errno, $errstr, $ldapConnTimeout)) {
				$conn = @ldap_connect('ldap://'.$host.'/');
				if ($conn) {
					return $conn;
				}
			}
		}
		$this->log('Cannot connect to LDAP Host: '.$ldapConf['LdapHost']);
		return false;
	}

	/**
	 * login
	 */
	function othLogin($params,$ignore_cookie=false) // username,password,group
	{	

		$controller = $this->_registry->getController();
    	$controller->getRequest()->getSession()->delete('accountTimeOutId');
		$controller->getRequest()->getSession()->delete('userTimeOutId');
		$params = $params[$this->user_model];

		 if($controller->getRequest()->getSession()->check('othAuth.'.$this->hashkey))
		 {
			 if(!$this->kill_old_login)
			 {
				 return 1;
			 }
		 }

		 if(($params == null) ||
			 !isset($params[$this->user_login_var]) ||
			 !isset($params[$this->user_passw_var]))
		 {
			 return 0;
		 }


		 $Sanitize = $this->_registry->getController()->loadComponent('Sanitize');

		 $login = $Sanitize->paranoid($params[$this->user_login_var],$this->allowedLoginChars);
		 $passw = $params[$this->user_passw_var];

		 if($login == "" || $passw == "")
		 {
			 return -1;
		 }
		 $authMethod = 'standard';

		 if(!$ignore_cookie and @$authMethod != 'ldap')
		 {
			 $passw = $this->_getHashOf($passw);
		 }

		 $gid_check_op = $this->_getGidOp();//($this->strict_gid_check)?'':'<=';
		 $conditions = array();

		$UserModel = TableRegistry::getTableLocator()->get('SaasAdmins');

		if (!in_array($authMethod, array('ldap', 'ntlm'))) {
			if(isset($params[$this->user_group_var]))
			 {
				 $this->gid = (int) Sanitize::paranoid($params[$this->user_group_var]);

				 // FIX
				 if( $this->gid < 1)
				 {
					 $this->gid = 1;
				 }
				 $conditions[$this->user_table_gid] = $gid_check_op.$this->gid;
			 }

			$conditions[$this->user_table_login] = $login;
			$conditions[$this->user_table_active] = 1;

		}

		
		$salt = '';
		$row = $UserModel->find()->where($conditions)->first();
		$useSalt = strrpos($row['passwd'], 'salt');
		$passUpgrade = strrpos($row['passwd'], 'sha2021');



		if(!empty($useSalt)){
			 $salt = substr($row['passwd'], 0, 16);
			 $passw = $this->_getHashOf($params[$this->user_passw_var], $salt);
		}else if(!empty($passUpgrade)){
			 $passw = $this->_getHashOf(md5($params[$this->user_passw_var])).'sha2021';
		}

		$this->log(var_export($passw, true));
		$this->log(var_export($row[$this->user_table_passw], true));
		if($passw !== $row[$this->user_table_passw]){
			$row = array();
		}

		if( empty($row) /* || $num_users != 1 */ )
		{
			$this->_saveLoginAttempts();
			return -2;
		} else {
			// Update the last visit date to now
			if(isset($this->user_table_last_visit)){
				$loginRecordModel = TableRegistry::getTableLocator()->get('SaasLoginRecords');
				
				$row[$this->user_table_last_visit] = date('Y-m-d H:i:s');
				$row['last_visit_from'] = $loginRecordModel->getClientIP();

				$res = $UserModel->save($row,false,array('last_visit_from', $this->user_table_last_visit));
				/**
				* now save login records
				*/
				$data = $loginRecordModel->newEntity([
				    'saas_admin_id' => $row['id'],
				    'ip' => $row['last_visit_from']
				]);
				$loginRecordModel->save($data);
			}

			if($controller->getRequest()->getSession()->check('SaasAdmin.BackupCodeId')){
				$mfaBackupCodeModel = TableRegistry::getTableLocator()->get('MfaBackupCodes');
				$back_code_id = $controller->getRequest()->getSession()->read('SaasAdmin.BackupCodeId');

				$backupUsed = $mfaBackupCodeModel->get($back_code_id, [
		            'contain' => [],
		        ]);
		        $backupUsed['used'] = 1;
				$mfaBackupCodeModel->save($backupUsed);
				$controller->getRequest()->getSession()->write('AppController.mfaAlert', true);
			}
			$saasAdminAuthGroupModel = TableRegistry::getTableLocator()->get('SaasAdminAuthGroups');
			$saasAuthGroupPermissionModel = TableRegistry::getTableLocator()->get('SaasAuthGroupPermissions');

			$saasAdminAuthGroupModel->hasOne('SaasAuthGroups', [
	            'foreignKey' => false,
	            'conditions' => ['SaasAuthGroups.id = SaasAdminAuthGroups.saas_auth_group_id'],
	        ]);

			$adminGroups = $saasAdminAuthGroupModel->find('all')->where(['saas_admin_id' => $row['id']])->contain('SaasAuthGroups')->toArray();
			

			$isAllowAllAction = array_filter($adminGroups, function ($group) {
			    return isset($group->saas_auth_group) && $group->saas_auth_group->allow_all_action === 'Y';
			});


			if(!empty($isAllowAllAction)){
				$row['permissionAction'] = 'allow_all_action';
				$row['advancePermission'] = 'grant_all';
			}else{
				$adminGroupIds = array_map(function ($group) {
				    return $group->saas_auth_group->id;
				}, $adminGroups);

				if(!empty($adminGroupIds)){
					$row['permissionAction'] = $saasAuthGroupPermissionModel->find('list', [
					    'keyField' => 'id',
					    'valueField' => 'action',
					])
					->where(['saas_auth_group_id IN' => $adminGroupIds])
					->toArray();
				}else{
					$row['permissionAction'] = [];
					return -5;
				}

				$row['advancePermission'] = $this->_advPermission($adminGroups);
			}
			$this->_saveSession($row);
			$back = isset($this->controller->params['url']['url'][$this->auth_url_redirect_var]);
			$url = $adminGroups[0]->saas_auth_group->home;
			$this->redirect('/'.$url, $back);
		}
	}

	function naoLogin($params,$ignore_cookie = false) // username,password,group
   	{
		 $params = $params[$this->user_model];

		 if($this->Session->valid() && $this->Session->check('othAuth.'.$this->hashkey))
		 {
		 	if(!$this->kill_old_login)
		 	{
		 		return 1;
		 	}
		 }

		 if($params == null ||
		 	!isset($params[$this->user_login_var]) ||
		 	!isset($params[$this->user_passw_var]))
		 {
		 	return 0;
		 }

		 uses('sanitize');
		 $login = Sanitize::paranoid($params[$this->user_login_var],$this->allowedLoginChars);
		 $passw = Sanitize::paranoid($params[$this->user_passw_var]);
		 if(isset($params[$this->user_group_var]))
		 {

		 	$this->gid = (int) Sanitize::paranoid($params[$this->user_group_var]);
			if( $this->gid < 1)
			{
				$this->gid = 1;
			}
		 }

		 if($login == "" || $passw == "")
		 {
		 	return -1;
		 }

		if(!$ignore_cookie)
		{
			$passw = $this->_getHashOf($passw);
		}

		$conditions = array(
							"{$this->user_model}.".$this->user_table_login => "$login",
							"{$this->user_model}.".$this->user_table_passw => "$passw",
							"{$this->user_model}.".$this->user_table_active => 1);

		$UserModel = new $this->user_model;
		$UserModel->unbindAll(array('belongsTo'=>array($this->group_model)));
		$UserModel->recursive = 2;

		$UserModel->{$this->group_model}->unbindAll(array('hasAndBelongsToMany'=>array($this->permission_model)));

		$row = $UserModel->find($conditions);

		$num_users = (int) $UserModel->find('count', array('conditions' => $conditions));

       $gids = array();

       if(!empty($row[$this->group_model])){
               foreach ($row[$this->group_model] as $group){
                       $gids[] = $group['level'];
               }
       }

       if($this->strict_gid_check)
       {
       		$allowed = in_array($this->gid,$gids);
       }
       else
       {
       		$allowed = false;
       		switch($this->gid_order)
       		{
       			case 'asc':
	       			foreach($gids as $gid)
	       			{
	       				if($this->gid >= $gid)
	       				{
	       					$allowed = true;
	       					break;
	       				}
	       			}
       			break;
       			case 'desc':
	       			foreach($gids as $gid)
	       			{
	       				if($this->gid >= $gid)
	       				{
	       					$allowed = true;
	       					break;
	       				}
	       			}
       			break;
       		}
       }

       if( empty($row) || $num_users != 1 || !$allowed)
       {
               $this->_saveLoginAttempts();
               return -2;
       }
       else
       {
			$this->_deleteLoginAttempts();

			if(!$ignore_cookie &&
			    !empty($params[$this->user_cookie_var]) )
			{
				$this->_saveCookie($row);
			}

			$this->_saveSession($row);

			// Update the last visit date to now
			if(isset($this->user_table_last_visit))
			{
				$row[$this->user_model][$this->user_table_last_visit] = date('Y-m-d H:i:s');
				$res = $UserModel->save($row,true,array($this->user_table_last_visit));
			}

			// 0.2.5 save history
			if($this->history_active)
			{
				$this->_addHistory($row);
			}

			$redirect_page = $this->access_page;
			foreach($row[$this->group_model] as $grp)
			{
				if($grp['level'] == $this->gid)
				{
					if(!empty($grp['redirect']))
					{
						$redirect_page = $grp['redirect'];
					}
				}
			}

			$this->redirect($redirect_page);

			return 1;
       }

	}

	// 0.2.5
	function _addHistory(&$row)
	{
		$data[$this->history_model]['username']  = $row[$this->user_model][$this->user_table_login];
		$data[$this->history_model]['fullname']  = $row[$this->user_model]['fullname'];
		$data[$this->history_model]['groupname'] = $row[$this->group_model]['name'];
		if(isset($row[$this->user_model][$this->user_table_last_visit]))
		{
			$data[$this->history_model]['visitdate'] = $row[$this->user_model][$this->user_table_last_visit];
		}else
		{
			$data[$this->history_model]['visitdate'] = date('Y-m-d H:i:s');
		}
		App::import('Model', $this->history_model);
		$HistoryModel = new $this->history_model;
		$HistoryModel->save($data);

	}
	function _saveSession($row)
	{
		 $login = $row[$this->user_table_login];
		 $passw = $row[$this->user_table_passw];
		 $gid   = $row[$this->user_table_gid];
		 $hk    = $this->_getHashOf($this->hashkey.$login.$passw/*.$gid*/);
		 $row['login_hash'] = $hk;
 		 $row['hashkey']    = $this->hashkey;
 		 /**
 		  * for emma, we added related permission here
 		  * thus we can adde entry permission in pgroup management,
 		  * and get all related permission work
		  *
		  * @todo improve or remove this in future, for now we dont need it (2008-10-27, Jerry)
 		  */
 		 //$this->_addRelatedPerms($row['Pgroup']['Permission']);

 		 $controller = $this->_registry->getController();
		 $controller->getRequest()->getSession()->write('othAuth.'.$this->hashkey,$row);
	

		$ss = $controller->getRequest()->getSession()->read();
	}	

	/**
	 * add related permission, manipulate var directly
	 * @param array &$perms
	 */
	function _addRelatedPerms(&$perms) {
		include(CONFIGS . 'emma_confs.php');
		$erp = $emma_related_perms;
		$i = 0;
		$aDate = date('Y-m-d H:i:s');
		//die(var_export($perms, true));
		foreach ($perms as $perm) {
			if (!empty($erp[$perm['name']])) {
				foreach ($erp[$perm['name']] as $addperm) {
					$perms[] = array(
						'id' => 20000000 + $i,
						'name' => $addperm,
						'created' => $aDate,
						'modified' => $aDate,
					);
					$i++;
				}
			}
		}
	}

	// null, true to delete the cookie
	function _saveCookie($row,$del = false)
	{
		if($this->cookie_active)
		{
			if(!$del)
			{
				$login  = $row[$this->user_model][$this->user_table_login];
				$passw  = $row[$this->user_model][$this->user_table_passw];

				$time   = strtotime($this->cookie_lifetime);
				$data   = $login.'|'.$passw;
				$data   = serialize($data);
				$data   = $this->encrypt($data);
				setcookie('othAuth',$data,$time,'/');
			}else
			{
				setcookie('othAuth','',strtotime('-999 day'),'/');
			}
		}
	}

	function _readCookie()
	{
		// does session exists
		if($this->Session->valid() && $this->Session->check('othAuth.'.$this->hashkey))
		{
			return;
		}
		if($this->cookie_active && isset($_COOKIE['othAuth'])) {

			$str = $_COOKIE['othAuth'];
			if (get_magic_quotes_gpc())
			{
				$str=stripslashes($str);
			}

			$str = $this->decrypt($str);

			$str = @unserialize($str);

			list($login,$passw) = explode('|',$str);

			$data[$this->user_model][$this->user_login_var] = $login;
			$data[$this->user_model][$this->user_passw_var] = $passw;
			$redirect_old = $this->auto_redirect;
			$this->auto_redirect = false;
			$ret = $this->_login($data,true);
			$this->auto_redirect = $redirect_old;
		}
	}

	// delete attempts after a successful login
	function _deleteLoginAttempts()
	{
		if($this->login_limit)
		{
			App::import('Model', $this->login_attempts_model);
			$Model =  new $this->login_attempts_model;
			$ip = $Model->getClientIP();
			$Model->del($ip);
			if($this->cookie_active)
			{
				setcookie('othAuth.login_attempts','',time() - 31536000,'/');
			}
		}

	}
	function _checkLoginAttempts()
	{
		if($this->login_limit)
		{
			App::import('model', $this->login_attempts_model);
			$Model =  new $this->login_attempts_model;
			$ip = $Model->getClientIP();
			// delete all expired and timedout records
			$del_sql = "DELETE FROM {$Model->useTable} WHERE expire <= NOW()";
			if($this->login_attempts_timeout > 0)
			{
				$timeout = $this->login_attempts_timeout * 60;
				// 1.5.4 fixed a bug here, thanks to PatDaMilla
				$del_sql .= " OR ( UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(created) > $timeout )";
				//
			}
			$Model->query($del_sql);

			$row = $Model->find(array($this->login_attempts_model.'.ip'=>$ip));

			if(!empty($row))
			{
				$num = $row[$this->login_attempts_model]['num'];

				$this->login_attempts_current_num = $num;

				if($num >= $this->login_attempts_num)
				{
					return false;
				}
			}else
			{
				$this->login_attempts_current_num = 0;
			}

			if($this->cookie_active && isset($_COOKIE['othAuth.login_attempts']))
			{
	            $cdata = $_COOKIE['othAuth.login_attempts'];
	            if (get_magic_quotes_gpc())
	            {
	                $cdata=stripslashes($cdata);
	            }

				$cdata = $this->decrypt($cdata);

	            $cdata = @unserialize($cdata);

	            $time      = $cdata['t'];
	            $num_tries = $cdata['n'];

	            if($num_tries >= $this->login_attempts_num)
				{
					return false;
				}

	            if($this->login_attempts_current_num == 0 && $num_tries > 0)
	            {
					$this->login_attempts_current_num = $num_tries;
	            }

			}
		}
		return true;
	}

	function _saveLoginAttempts()
	{
		if($this->login_limit)
		{
			$num_tries = $this->login_attempts_current_num + 1;

			if (!is_numeric($this->login_locked_out))
			{
				$keep_for = (int) strtotime($this->login_locked_out);
				$time   = ($keep_for > 0 ? $keep_for : 999999999);
			}
			else
			{
				$keep_for = $this->login_locked_out;
				$time   = time() + ($keep_for > 0 ? $keep_for : 999999999);
			}

			//die(date("Y-m-d H:i:s",$keep_for));
			App::import('Model', $this->login_attempts_model);
			$Model =  new $this->login_attempts_model;
			$expire = date("Y-m-d H:i:s", $time);
			$ip = $Model->getClientIP();
			//die(pr($expire));
			$data[$this->login_attempts_model]['ip']     = $ip;
			$data[$this->login_attempts_model]['expire'] = $expire;
			$data[$this->login_attempts_model]['num']    = $num_tries;

			if($num_tries <= 1) // dunno why the model doesn't handle this
			{
				$data[$this->login_attempts_model]['created'] = date("Y-m-d H:i:s");
			}
			$Model->save($data);

			if($this->cookie_active)
			{
				$cdata = $this->encrypt(serialize(array('t'=>time(),'n'=>$num_tries)));
				setcookie('othAuth.login_attempts',$cdata,$time,'/');
			}
		}
	}

	function __notcurrent($page)
	{
		if($page == "") return false;
		$controller = $this->_registry->getController();
		$c = strtolower(Inflector::underscore($controller->getRequest()->getParam('controller')));
		$a = strtolower($controller->getRequest()->getParam('action'));
		$page = strtolower($page.'/');
		$c_a = $this->_handleCakeAdmin($c,$a);
		if($page[0] == '/')
		{
			$c_a = '/'.$c_a;
		}
		//die($c_a.' '.$page);
		$not_current = strpos($page,$c_a);
		// !== is required, $not_current might be boolean(false)
		return ((!is_int($not_current)) || ($not_current !== 0));
	}

 	function redirect($page = "",$back = false)
   {
        if($page == "") {
          //$page = $this->redirect_page;
          $page = $this->logout_page;
        }
        //DEBUG(__FUNCTION__.'::'.__LINE__);
        if(isset($this->auth_url_redirect_var))
        {
            if(!isset($this->controller->params['url'][$this->auth_url_redirect_var]))
            {	

            	 $controller = $this->_registry->getController();
                if($back == true)
                {
                  // ==== Ritesh: modified from here ==========
                  $frompage = '/';
                  if(isset($this->controller->params['url']['url'])) {
                    $frompage .= $this->controller->params['url']['url'];  //if url is set then set frompage to url
                    $parameters = $this->controller->params['url'];   // get url array
                    unset($parameters['url']);
                    $para = array();
                    foreach($parameters as $key => $value) {
                      //for each parameter of the url create key=value string
                      $para[] =  $key . '=' . $value;
                    }
                    //DEBUG(__FUNCTION__.'::'.__LINE__);
                    if(count($para) > 0){
                      $frompage .= '?' . implode('&',$para); //attach parameters to the frompage
                    }
                  }
                  //DEBUG(__FUNCTION__.'::'.__LINE__);
                  $controller->getRequest()->getSession()->write('othAuth.frompage',$frompage);
                  if($this->show_auth_url_redirect_var) {
	            		  $page .= "?".$this->auth_url_redirect_var."=".$frompage;
                  }
	            	  //====== end of modification =================
                } else {


                    if($controller->getRequest()->getSession()->check('othAuth.frompage'))
                    {
                        //DEBUG(__FUNCTION__.'::'.__LINE__);
                        $page = $controller->getRequest()->getSession()->read('othAuth.frompage');
                        $controller->getRequest()->getSession()->delete('othAuth.frompage');
                    }
                }
            }

        }
        //DEBUG(__FUNCTION__.'::'.__LINE__);

        if($this->__notcurrent($page))
        {
           if ($this->RequestHandler->isAjax)
           {
              // setAjax is deprecated in 1.2
              if($this->is_11()) //1.1
              {
                $this->RequestHandler->setAjax($this->controller);
              }else // 1.2
              {
                $this->controller->layout = $this->RequestHandler->ajaxLayout;
                $this->RequestHandler->respondAs('html', array('charset' => 'UTF-8'));
              }

              // Brute force ! you've got a better way ?
              echo '<script type="text/javascript">window.location = "'.
                   $this->url($page).
                   '"</script>';
              exit;
           }
           else
           {	
           		$controller = $this->_registry->getController();
                return $controller->redirect($page);
                   //DEBUG($page);
                   /*
                   if ($back != true) {
                     $this->controller->redirect($page);
                   }*/
                exit;
           }
        }
    }



    // Logout the user
    //FIX:
    //   logout_page is the logout action OR the the action to redirect to after logout ?
    function logout ($kill_cookie = true)
	{
		$us = 'othAuth.'.$this->hashkey;

		$controller = $this->_registry->getController();

		if($controller->getRequest()->getSession()->check($us))
		{
			$ses = $controller->getRequest()->getSession()->read($us);

			if(!empty($ses) && is_array($ses))
			{
				// two logins of different hashkeys can exist
				if($this->hashkey == $ses[$this->user_model]['hashkey'])
				{
					$controller->getRequest()->getSession()->delete($us);
					$controller->getRequest()->getSession()->delete('othAuth.frompage');
					/*
					$o = $this->Session->check('othAuth');
					if( is_array( $o ) && empty( $o  ))
					{
						$this->Session->del('othAuth');
					}
					*/
					//unset($_SESSION['othAuth'][$this->hashkey]);
					if($kill_cookie)
					{
						$this->_saveCookie(null,true);
					}
					if($this->auto_redirect == true)
					{
						// check if logout_page is the action where logout is called!
						if(!empty($this->logout_page))
						{
							$this->redirect($this->logout_page);
						}
					}
					return true;
				}
			}
		}
		return false;
    }


	// Confirms that an existing login is still valid
	function check()
  {
		// try to read cookie
		$this->_readCookie();
		// is there a restriction list && action is in
		if($this->_validRestrictions())
		{
			$us 	   = 'othAuth.'.$this->hashkey;

			// does session exists
			if($this->Session->valid() &&
				$this->Session->check($us))
			{
				$ses 	   = $this->Session->read($us);
				$login     = $ses["{$this->user_model}"][$this->user_table_login];
				$password  = $ses["{$this->user_model}"][$this->user_table_passw];
				$gid       = $ses["{$this->user_model}"][$this->user_table_gid];
				$hk        = $ses["{$this->user_model}"]['login_hash'];

				// is user invalid
				if ($this->_getHashOf($this->hashkey.$login.$password/*.$gid*/) != $hk)
				{
					$this->logout();
					return false;
				}

				switch ($this->mode)
				{
				case 'oth':
					$permi = $this->_othCheckPermission($ses);

					break;
				case 'nao':
					$permi = $this->_othCheckPermission($ses,true);
					break;
				case 'acl':
					$permi = $this->_aclCheckPermission($ses);
					break;
				default:
					$permi = $this->_othCheckPermission($ses);
				}

				/**
				 * temporarily make permission check not working
				 * only session checking will work
				 */
				$permi = true;

				// check permissions on the current controller/action/p/a/r/a/m/s
				if(!$permi)
				{
					if($this->auto_redirect == true)
					{
						// should probably add $this->noaccess_page too or just flash
						//print_r($this->controller->params);
						$this->redirect($this->noaccess_page,true);
					}
					return false;
				}

				return true;

			}
			if($this->auto_redirect == true)
			{
				$this->redirect($this->login_page, true);
			}
			return false;
		}

		return true;
    }

	function _validRestrictions()
	{
		$isset   = isset($this->controller->othAuthRestrictions);
		if($isset)
		{
			$oth_res = $this->controller->othAuthRestrictions;

			if(is_string($oth_res))
			{
				if(($oth_res === "*") ||(
				defined('CAKE_ADMIN_AUTH_ONLY') && (($oth_res === CAKE_ADMIN_AUTH_ONLY) || $this->isCakeAdminAction())))
				{
					if(
					   $this->__notcurrent($this->login_page) &&
					   $this->__notcurrent($this->logout_page))
					{
						//die('here');
						return true;
					}
				}

			}
			elseif(is_array($oth_res))
			{
				if(defined('CAKE_ADMIN_AUTH_ONLY'))
				{
					if(in_array(CAKE_ADMIN_AUTH_ONLY,$oth_res))
					{
						if($this->isCakeAdminAction())
						{
							if($this->__notcurrent($this->login_page) &&
							   $this->__notcurrent($this->logout_page))
							{
								return true;
							}
						}
					}
				}
				foreach($oth_res as $r)
				{
					$pos = strpos($r."/",$this->controller->action."/");
					if($pos === 0)
					{
						return true;
					}
				}
			}
		}

		return false;
	}

	function _othCheckPermission(&$ses,$multi = false)
	{

		$c   = strtolower(Inflector::underscore($this->controller->name));
		$a   = strtolower($this->controller->action);
		$h   = strtolower($this->controller->here);
		$c_a = $this->_handleCakeAdmin($c,$a);// controller/admin_action -> admin/controller/action

		// extract params
		$aa  =  substr( $c_a, strpos($c_a,'/'));

		$params = isset($this->controller->params['pass']) ? implode('/',$this->controller->params['pass']): '';

		$c_a_p = $c_a.$params;

		$return = false;

		if(!isset($ses[$this->group_model][$this->permission_model]))
		{
			return false;
		}
		if(!$multi)
		{
			$ses_perms = $ses[$this->group_model][$this->permission_model];

		}else
		{
           foreach ($ses[$this->group_model] as $groups)
           {
               if(isset($groups[$this->permission_model])){
                       $ses_perms = am($ses_perms, $groups[$this->permission_model]);
               }
           }
		}

		// quickly check if the group has full access (*) or
		// current_controller/* or CAKE_ADMIN_AUTH_ONLY/current_controller/*
		// full params check isn't supported atm
		foreach($ses_perms as $sp)
		{
			if($sp['name'] == '*')
			{
				return true;
			}else
			{
				$sp_name = strtolower($sp['name']);
				$perm_parts = explode('/',$sp_name);
				// users/edit/1 users/edit/*
				//  users/* users/*

				if(defined('CAKE_ADMIN_AUTH_ONLY'))
				{

					if((count($perm_parts) > 1)  &&
					   ($perm_parts[0] == CAKE_ADMIN_AUTH_ONLY) &&
					   ($perm_parts[1] == $c) &&
					   (@$perm_parts[2] == "*"))
					{
						return true;
					}
				}
				//else
				//{
					if((count($perm_parts) > 1)  &&
					   ($perm_parts[0] == $c) &&
					   ($perm_parts[1] == "*"))
					{
						return true;
					}
				//}

			}
		}


		if(is_string($this->controller->othAuthRestrictions))
		{
			$is_checkall   = $this->controller->othAuthRestrictions === "*";
			$is_cake_admin = defined('CAKE_ADMIN_AUTH_ONLY') && ($this->controller->othAuthRestrictions === CAKE_ADMIN_AUTH_ONLY);
			if($is_checkall || $is_cake_admin)
			{
				foreach($ses_perms as $p)
				{
					if(strpos($c_a_p,strtolower($p['name'])) === 0)
					{
						$return = true;
						break;
					}
				}
			}
		}
		else
		{
			$a_p_in_array = in_array($a.'/'.$params, $this->controller->othAuthRestrictions);

			// if current url is restricted, do a strict compare
			// ex if current url action/p and current/p is in the list
			// then the user need to have it in perms
			// current/p/s current/p
			if($a_p_in_array)
			{

				foreach($ses_perms as $p)
				{
					if($c_a_p == strtolower($p['name']))
					{
						$return = true;
						break;
					}
				}
			}
			// allow a user with permssion on the current action to access deeper levels
			// ex: user access = 'action', allow 'action/p'
			else
			{
				foreach($ses_perms as $p)
				{
					if(strpos($c_a_p,strtolower($p['name'])) === 0)
					{
						$return = true;
						break;
					}
				}
			}
		}
		return $return;
	}

   function _aclCheckPermission(&$ses)
   {
           //die('c');
           $c   = Inflector::underscore($this->controller->name);
           $a   = $this->controller->action;

           $aco = "$c:$a";

           $login = $ses["{$this->user_model}"][$this->user_table_login];

           return $this->_aclCheckAccess($login, $aco);
   }

   function _aclCheckAccess($aro_alias, $aco)
   {
           // Check access using the component:
           $access = $this->Acl->check($aro_alias, $aco, $action = "*");
           if ($access === false)
           {
                   return false;
           }
           else
           {
                   return true;
           }
   }

	function _handleCakeAdmin($c,$a)
	{
		if(defined('CAKE_ADMIN_AUTH_ONLY'))
		{
			$strpos = strpos($a,CAKE_ADMIN_AUTH_ONLY.'_');
			if($strpos === 0)
			{
				$function = substr($a,strlen(CAKE_ADMIN_AUTH_ONLY.'_'));
				if($c == null) return $function.'/';
				$c_a = CAKE_ADMIN_AUTH_ONLY.'/'.$c.'/'.$function.'/';
				return $c_a;
			}else
			{
				if($c == null) return $a.'/';
			}
		}
		return $c.'/'.$a.'/';
	}

	function getSafeCakeAdminAction()
	{
		if(defined('CAKE_ADMIN_AUTH_ONLY'))
		{
			$a = $this->controller->action;
			$strpos = strpos($a,CAKE_ADMIN_AUTH_ONLY.'_');
			if($strpos === 0)
			{
				$function = substr($a,strlen(CAKE_ADMIN_AUTH_ONLY.'_'));

				return $function;
			}
		}
		return $this->controller->action;
	}

	function isCakeAdminAction()
	{
		if(defined('CAKE_ADMIN_AUTH_ONLY'))
		{
			$a = $this->controller->action;
			$strpos = strpos($a,CAKE_ADMIN_AUTH_ONLY.'_');
			if($strpos === 0)
			{
				return true;
			}
		}
		return false;
	}

	// helper methods
	function user($arg)
	{
		$us = 'othAuth.'.$this->hashkey;
		// does session exists
		if($this->Session->valid() && $this->Session->check($us))
		{
			$ses = $this->Session->read($us);
			if(isset($ses["{$this->user_model}"][$arg]))
			{
				return $ses["{$this->user_model}"][$arg];
			}
			else
			{
				return false;
			}
		}
		return false;
	}

	// helper methods
	function group($arg)
	{
		$us = 'othAuth.'.$this->hashkey;
		// does session exists
		if($this->Session->valid() && $this->Session->check($us))
		{
			$ses = $this->Session->read($us);
			if(isset($ses["{$this->group_model}"][$arg]))
			{
				return $ses["{$this->group_model}"][$arg];
			}
			else
			{
				return false;
			}
		}
		return false;
	}


	// helper methods
	function permission($arg)
	{
		$us = 'othAuth.'.$this->hashkey;
		// does session exists
		if($this->Session->valid() && $this->Session->check($us))
		{
			$ses = $this->Session->read($us);
			if(isset($ses[$this->group_model][$this->permission_model]))
			{
				$ret = array();
				if(is_array($ses[$this->group_model][$this->permission_model]))
				{
					for($i = 0; $i < count($ses[$this->group_model][$this->permission_model]); $i++ )
					{
						$ret[] = $ses[$this->group_model][$this->permission_model][$i][$arg];
					}
				}
				return $ret;
			}
			else
			{
				return false;
			}
		}
		return false;
	}

	/**
	 * check if has permission on given url
	 *
	 * @param string $val
	 * @return bool
	 */
	function hasPermission($val) {
		$perms = $this->permission('name');
		if (in_array('*', $perms)) {
			return true;
		}
		if (in_array($val, $perms)) {
			return true;
		}

		$vals = explode('/', $val);
		$val = '';
		for ($i = 0; $i < count($vals); $i++) {
			if ($i) {
				$val .= '/';
			}
			$val .= $vals[$i];
			if (in_array($val, $perms)) {
				return true;
			}
		}
		return false;
	}

	function getData($arg = '',$only = true)
	{
		$us = 'othAuth.'.$this->hashkey;
		// does session exists
		if($this->Session->valid() && $this->Session->check($us))
		{
			$data = $this->Session->read($us);
			$arg = strtolower($arg);

			if($arg == 'user')
			{
				$data = $data['User'];

			}elseif($arg == 'group')
			{
				if($only)
				{
					unset($data['Group']['Permission']);
				}

				$data = $data['Group'];

			}elseif($arg == 'permission')
			{
				$data = $data['Group']['Permission'];
			}

			return $data;
		}
		return false;
	}

	// passes data to the view to be used by the helper
	function _passAuthData()
	{

		$data = get_object_vars($this);

		unset($data['controller']);
		unset($data['components']);
		unset($data['Session']);
		unset($data['RequestHandler']);
		$controller = $this->_registry->getController();
		$controller->set('othAuth_data',$data);
		/**
		 * since pass data to helper by set data will be overried by
		 * setflash with layout template, so we pass data by session again
		 */
		// $controller->getRequest()->getSession()->write('emma_auth_data', $data);
	}


	function encrypt($string)
	{
    	$key = $this->hashkey;
    	$result = '';
    	for($i=0; $i<strlen($string); $i++) {
      		$char = substr($string, $i, 1);
     		$keychar = substr($key, ($i % strlen($key))-1, 1);
     		$char = chr(ord($char)+ord($keychar));
     		$result.=$char;
   		}

   		return base64_encode($result);
  	}

  	function decrypt($string)
  	{
   		$key = $this->hashkey;
   		$result = '';
   		$string = base64_decode($string);

   		for($i=0; $i<strlen($string); $i++) {
     		$char = substr($string, $i, 1);
     		$keychar = substr($key, ($i % strlen($key))-1, 1);
     		$char = chr(ord($char)-ord($keychar));
     		$result.=$char;
   		}

   		return $result;
  }
	function getMsg($errno) {
		$msgs = array(
			2 => '密碼已修改，請重新登入',
			1 => 'You are already logged in.',
			0 => 'Please login !',
			-1 => '帳號/名稱空白',
			-2 => '帳密/密碼或兩階段驗證碼錯誤',
			-3 => 'Too many login attempts.',
			-4 => '系統異常，請透過正常方式登入',
			-5 => '該帳號未設定權限群組，請聯繫系統管理員協助設定',
			-19 => '登入帳號無人員資料',
			-20 => '該人員已經離職',
			-31 => '無法設定認證協定版本為 3',
			-38 => '認證通過，但認證伺服器未傳回有效使用者資料',
			-39 => '系統並未提供LDAP認證函式庫',
		);
		if (isset($msgs[$errno])) {
			return $msgs[$errno];
		}

		switch($errno) {
		case -1:
			return $this->user_login_var."/".$this->user_passw_var." empty";
			break;
		default:
			return "Invalid error ID";
			break;
		}
	}

	/*
	 * Create the User model to be used in login methods.
	 */
	function _createModel()
	{
		// since we don't know if the models have extra associations we need to
		// unbind all the models, and bind only the ones we're interested in
		// mainly for performance ( and security )


		/*if (ClassRegistry::isKeySet($this->user_model))
		{
			$UserModel = ClassRegistry::getObject($this->user_model);
		}
		else
		{
			App::import('Model', $this->user_model);
			$UserModel = new $this->user_model;

		}

        $forUser  = array('belongsTo'=>array($this->group_model),
                          'hasOne'=>array(),
                          'hasMany'=>array(),
                          'hasAndBelongsToMany'=>array()
                         );
        $forGroup = array('belongsTo'=>array(),
                          'hasOne'=>array(),
                          'hasMany'=>array(),
                          'hasAndBelongsToMany'=>array($this->permission_model)
                         );
        $forPerm  =  array('belongsTo'=>array(),
                           'hasOne'=>array(),
                           'hasMany'=>array(),
                           'hasAndBelongsToMany'=>array()
                          );


		$forUser  = $this->_mergeModelsToKeep($forUser,$this->allowedAssocUserModels);
		$forGroup = $this->_mergeModelsToKeep($forGroup,$this->allowedAssocGroupModels);
		$forPerm  = $this->_mergeModelsToKeep($forPerm,$this->allowedAssocPermissionModels);

		// TODO:
		// should save the old recursive for the three models
		// add default recursives for user 2, for group 1, for permission 1
		// so that extra models can be fetched if supplied
		$UserModel->recursive = 2;
		$UserModel->unbindAll($forUser);
		$UserModel->{$this->group_model}->unbindAll($forGroup);

		$UserModel->{$this->group_model}->{$this->permission_model}->unbindAll($forPerm);
		*/
		$UserModel = ClassRegistry::getObject($this->user_model);
		return $UserModel;
	}

	function _mergeModelsToKeep($initial,$toAdd)
	{
		if(!empty($toAdd))
		{
			if(isset($toAdd['belongsTo']))
			{
				$initial['belongsTo'] =
				am($initial['belongsTo'],$toAdd['belongsTo']);
			}
			if(isset($toAdd['hasOne']))
			{
				$initial['hasOne'] = am($initial['hasOne'],	$toAdd['hasOne']);
			}
			if(isset($toAdd['hasMany']))
			{
				$initial['hasMany'] = am($initial['hasMany'],	$toAdd['hasMany']);
			}
			if(isset($toAdd['hasAndBelongsToMany']))
			{
				$initial['hasAndBelongsToMany'] = am($initial['hasAndBelongsToMany'],
													 $toAdd['hasAndBelongsToMany']);
			}
		}

		return $initial;
	}

	// is it cake version 1.1 ?
    function is_11()
    {
    	return (function_exists('strip_plugin'));
    }
   function url($url = null)
   {
		if($this->is_11()) // 1.2 doesn't have strip_plugin
        {
           $base = strip_plugin($this->controller->base, $this->controller->plugin);

           if (empty($url))
           {
                   return $this->controller->here;
           }
           elseif ($url[0] == '/')
           {
                   $output = $base . $url;
           }
           else
           {
                   $output = $base.'/'.strtolower($this->controller->params['controller']).'/'.$url;
           }
           return preg_replace('/&([^a])/', '&amp;\1', $output);
        }
        else
        {
        	return Router::url($url, false); // for 1.2
        }
   }

	/**
	 * check if all required key is set in ldap configurations
	 * @param array $conf
	 * @return bool
	 */
	function checkLdapConf($conf) {
		$keys = array('LdapHost', 'LdapConnTimeout', 'LdapDomain', 'LdapDn', 'LdapAccField', 'LdapAccNamePrefix', 'LdapKeyField');
		foreach ($keys as $key) {
			if (! isset($conf[$key])) {
				return false;
			}
		}
		return true;
	}

	function kickAccount($accountId, $sessCheck) {
		if (! class_exists($this->user_model)) {
			App::import('Model', $this->user_model);
		}
		$model = new $this->user_model;
		$result = $model->find('first', array('recursive' => -1, 'fields' => array('chkval', 'last_visit_from'), 'conditions' => array('id' => $accountId)));
		if ($result[$this->user_model]['chkval'] != $sessCheck) {
			if (!empty($result[$this->user_model]['last_visit_from'])) {
				$this->Session->write('Accounts.logoutMsg', '您已經在其他電腦('.$result[$this->user_model]['last_visit_from'].')登入');
			} else {
				$this->Session->write('Accounts.logoutMsg', '您已經在其他電腦登入');
			}
			$this->controller->redirect(array('controller' => 'Accounts', 'action' => 'logout'));
		}
	}

	function _passwdCompare($passwd, $hashPasswd = null, $id = null, $model = 'SaasAdmins'){
		if(empty($hashPasswd) && !empty($id)){
			if (! class_exists($model)) {
				App::import('Model', $model);
			}
			$useModel = new $model;
			$conditions[$model.'.id'] = $id;
			if($model == 'SaasAdmin'){
				$conditions[$model.'.active'] = 1;
			}
			$salt = '';
			$row = $useModel->find('first', array('conditions' => $conditions));
			$hashPasswd = $row[$model]['passwd'];
		}

		if(empty($hashPasswd) || empty($passwd)){
			return false;
		}

		$useSalt = strrpos($hashPasswd, 'salt');
		$passUpgrade = strrpos($hashPasswd, 'sha2021');



		if(!empty($useSalt)){
			$salt = substr($hashPasswd, 0, 16);
			$passw = $this->_getHashOf($passwd, $salt);
		}else if(!empty($passUpgrade)){
			$passw = $this->_getHashOf(md5($passwd)).'sha2021';
		}

		$this->log(var_export($passw, true));
		$this->log(var_export($hashPasswd, true));

		if($passw !== $hashPasswd){
			return false;
		}
		return true;
	}

	function _advPermission($adminGroups = array()){
		$finalPermission = array();

		foreach ($adminGroups as $v) {
			$advancePermissions = json_decode($v->saas_auth_group->advance_permission, true);
			foreach ($advancePermissions as $k => $permissions) {
				$finalPermission[$k] = empty($finalPermission[$k])?array():$finalPermission[$k];
				$finalPermission[$k] = array_merge($finalPermission[$k], array_diff($permissions, array('', 0)));

			}
		}
		return $finalPermission;
	}
}

?>
