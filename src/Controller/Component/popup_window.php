<?php
/**
 * $Id: popup_window.php,v 1.1.4.2.2.1 2023/10/26 10:30:32 ashinjuang Exp $
 * $Author: ashinjuang $
 * $Date: 2023/10/26 10:30:32 $
 */    
/**
 * popup window component
 */
require_once(LIBS.DS.'component_object.php');
class PopupWindowComponent extends ComponentObject {
	var $name = 'PopupWindowComponent';
	var $params = null;
	var $enable = false;


	/**
	 * initialization
	 */
	function initialize(&$controller) {
		$this->controller = &$controller;
		return true;
	}
	/**
	 * startup procedures
	 */
	function startup(&$controller) {
	}

	function enable($conf = null) {
		$this->enable = true;

		$conf = array_merge(array('text' => 'Popup Text', 'type' => 'alert'), $conf);

		$this->controller->set('conf', $conf);
	}

	function disable() {
		$this->enable = false;
	}

	function beforeRender(&$controller)
	{
		# code...
		if ($this->enable) {
			$this->controller->set('enablePopupWindow', 1);
		}
	}
}
?>
