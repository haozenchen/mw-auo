<?php
/**
 * $Id: com_checkbox.php,v 2.3.32.2 2024/05/16 03:51:34 ashinjuang Exp $
 * $Author: ashinjuang $
 * $Date: 2024/05/16 03:51:34 $
 * 
 * check box component to help controller deal with checkboxes
 *  thus developer can create multiple checkbox and read 
 *  the data easily
 *   
 * @copyright Copyright 2007, Fonsen Technology Ltd. Corp.
 */
require_once(LIBS.DS.'component_object.php');
class ComCheckboxComponent extends ComponentObject{
	var $controller = true;
	/**
	 * need Session component for session data processing
	 */     
	var $components = array('Session');

	/**
	 * initialization
	 */     
	function startup(&$controller) {
		$this->controller = $controller;
	}

	/**
	 * extract chechbox data from form
	 * @param string $modelName
	 * @return array
	 */     
	function extractCBXIds($modelName = null) {
		$cbxIds = array();
		if (isset($this->controller->data[$modelName])) {
			foreach ($this->controller->data[$modelName] as $key => $value) {
				if (!empty($value) and (substr($key, 0, 4) === 'cbx_')) {
					$cbxIds[] = (int)substr($key, 4);
				}
			}
		}

		return $cbxIds;
	}

	function choice($modelName = null, $name = null) {
		$cbxIds = array();
		if (isset($this->controller->data[$modelName])) {
			foreach ($this->controller->data[$modelName] as $key => $value) {
				if (!empty($value) and (substr($key, 0, 4) === $name)) {
					$cbxIds[] = (int)substr($key, 4);
				}
			}
		}

		return $cbxIds;
	}  

	function writeChoice($modelName = null,  $addStr = '', $data = null,$name = null ) {
		if (empty($data)) {
			$data = $this->choice($modelName,$name);
		}    
		if (! is_array($data)) {
			return false;
		}
		$this->Session->write($name . $modelName . $addStr, $data);
		return true;
	}


	function readChoice($modelName = null,  $addStr = '',$name = null ) {
		$value = $this->Session->read($name . $modelName . $addStr);
		// $this->Session->del($name . $modelName . $addStr);
		return $value;
	}

	function delChoice($modelName = null,  $addStr = '',$name = null){
		$this->Session->del($name . $modelName . $addStr);
	}
	/**
	 * write checkbox data into session
	 * @param string $modelName
	 * @param string $addStr just for differentiation consideration
	 * @param array $data if not given, extract myself
	 */     
	function writeCBXIds($modelName = null,  $addStr = '', $data = null) {
		if (empty($data)) {
			$data = $this->extractCBXIds($modelName);
		}

		if (! is_array($data)) {
			return false;
		}
		$this->Session->write('Emma.CBX_' . $modelName . $addStr, $data);
		return true;
	}

	function readCBX($modelName = null,  $addStr = '') {
		$value = $this->Session->read('Emma.CBX_' . $modelName . $addStr);
		// $this->Session->del('Emma.CBX_' . $modelName . $addStr);
		return $value;
	}
	/**
	 * read checkbox data from session
	 *  will parse data into array
	 *  will delete data after read
	 * @param string $modelName
	 * @param string $addStr
	 * @return array   
	 */   
	function readCBXIds($modelName = null,  $addStr = '') {
		$value = $this->Session->read('Emma.CBX_' . $modelName . $addStr);
		$this->Session->del('Emma.CBX_' . $modelName . $addStr);
		return $value;
	}

	/**
	 * stored paginate checkbox in some form field
	 * @param array $data Form data
	 * @param string $storedFieldName field name like 'User.selected'
	 * @param array $params Parameters like array('mode' => 'model.chk.id', 'model' => 'SelectedUser')
	 * @param string $checkAllFieldName field that used for check all, if set, field will be unset here
	 * @return array
	 */
	function getDataOfPaginateCheckbox($data = null,  $storedFieldName = null, $params = null, $checkAllFieldName = '') {
		list($sfModel, $sfField) = explode('.', $storedFieldName);

		if (!empty($checkAllFieldName)) {
			list($c1, $c2) = explode('.', $checkAllFieldName);
			unset($data[$c1][$c2]);
		}
		if (empty($data) or empty($params) or !isset($params['mode'])) {
			return $data;
		}
		if (empty($data[$sfModel][$sfField])) {
			$stored = array();
		} else {
			$stored = unserialize($data[$sfModel][$sfField]);
			if ($stored === false) {
				$stored = array();	// unserializable, no data
			}
		}
		$serialize = true;
		switch ($params['mode']) {
		case 'model.chk.id':
			// not support yet
			return $data;
		case 'model.id.chk':
			$params = array_merge(array('chk' => 'chk', 'empty' => false), (array)$params);
			extract($params);
			if (empty($stored)) {
				$stored[$model] = array();
			}
			$checked = array();
			/**
			 * merge data, use + to keep index
			 * in +, prior data value with same key will be preserved
			 * so data in front can make sure uncheck remembers
			 */
			$stored[$model] = $data[$model] + $stored[$model];
			if (! $empty) {
				// not allow empty value
				foreach ($stored[$model] as $id => $c) {
					if ($c[$chk]) {
						$checked[$id] = $c;
					}
				}
				$stored[$model] = $checked;
			}
			$data[$model] = $stored[$model];
			$stored = ($serialize) ? serialize($stored) : $stored;
			$data[$sfModel][$sfField] = $stored;
			return $data;
			break;
		case 'default':
			return $data;
		}
	}

}
?>
