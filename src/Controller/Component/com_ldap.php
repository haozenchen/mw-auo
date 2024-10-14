<?php 

/**
 * $Id: com_ldap.php,v 1.2.34.1.4.2 2024/05/16 03:49:30 ashinjuang Exp $
 * $Author: ashinjuang $
 * $Date: 2024/05/16 03:49:30 $
 */
/**
 * @copyright  Copyright 2007-2010, Fonsen Technology Ltd. Corp.
 */
require_once(LIBS.DS.'component_object.php');
class ComLdapComponent extends ComponentObject {

	/**
	 * build ldap connection
	 * can try several host (if given in comma separated, like 127.0.0.1,192.168.1.1)
	 * @param array $ldapConf
	 * @param string $ldapUser
	 * @param string $ldapPsw
	 */
   	function _ldapConn($ldapConf = null,  $ldapUser = null, $ldapPsw = null) {
		$ldapConnTimeout = ife($ldapConf['LdapConnTimeout'], $ldapConf['LdapConnTimeout'], 8);
		if (empty($ldapConf['LdapHost'])) {
			return false;
		}
		if (strpos($ldapConf['LdapHost'], ',')) {
			$hosts = preg_split('/,/', $ldapConf['LdapHost']);
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
	 * check if all required key is set in ldap configurations
	 * @param array $conf
	 * @return bool
	 */
	function checkLdapConf($conf = null) {
		$keys = array('LdapHost', 'LdapConnTimeout', 'LdapDomain', 'LdapDn', 'LdapAccField', 'LdapAccNamePrefix', 'LdapKeyField');
		foreach ($keys as $key) {
			if (! isset($conf[$key])) {
				return false;
			}
		}
		return true;
	}

	function setConf($ldapConf = null) {
		$this->ldapConf = $ldapConf;
	}

	function checkLib() {
		/**
		 * make sure function works
		 */
		if (! function_exists('ldap_connect')) {
			$this->log('Function ldap_connect() not exists.');
			$this->errCode = -39;
			return false;
		}
	}

	function connLdap($ldapUser = null,  $ldapPsw = null) {
		if (empty($this->ldapConf)) {
			$this->errCode = -99;
			return false;
		}
		$ldapConf = $this->ldapConf;
		$ldapConn = false;
		if (!empty($ldapConf) and $this->checkLdapConf($ldapConf) === true) {
			$ldapConn = $this->_ldapConn($ldapConf, $ldapUser, $ldapPsw);
		}
		return $ldapConn;
	}

	function bindLdap($ldapConn = null,  $ldapUser = null, $ldapPsw = null) {
		if (! @ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3)) {
			$this->log('Cannot set LDAP Protocol Version 3');
			$this->errCode = -31;
			return false;
		}
		if (@ldap_bind($ldapConn, $ldapUser, $ldapPsw)) {
			return true;
		}
		return false;
	}

	function getEntries($ldapConn = null,  $login = null) {
		$ldapConf = $this->ldapConf;
		/**
		 * access granted, user exists
		 */
		$filter = '('.$ldapConf['LdapAccField'].'='.$login.')';
		$ldapCutStr = explode(';', $ldapConf['LdapDn']);
		$search = '';
		
		for($i=0; $i<count($ldapCutStr); $i++) {
			$search = ldap_search($ldapConn, $ldapCutStr[$i], $filter);
			$entries = ldap_get_entries($ldapConn, $search);
			if(@$entries['count'] != 0) {break;}
		}
		
		if (@$entries['count'] = 0) {
			// this should not happen, user data might be error or field name error
			$this->log('Strange, LDAP Entry count is 0');
			$this->errCode = -38;
			return false;
		}
		return $entries;
	}
}
