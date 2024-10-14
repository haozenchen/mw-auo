<?php 

/**
 * $Id: com_custom.php,v 1.1.36.1 2023/10/26 10:30:32 ashinjuang Exp $
 * $Author: ashinjuang $
 * $Date: 2023/10/26 10:30:32 $
 */
/**
 * @copyright  Copyright 2009-2011, Fonsen Technology Ltd. Corp.
 */
require_once(LIBS.DS.'component_object.php');
class ComCustomComponent extends ComponentObject {
	var $controller = null;
	var $cfModel = null;

	function startup(&$controller) {
		$this->controller = &$controller;
		App::import('Model', 'CustomField');
		$this->_cfModel = new CustomField;
	}

	/**
	 * make view support custom fields
	 * @param object $model
	 */
	function custom($model = null) {
		$this->controller->params['_customFields'] = false;
		if (!empty($model)) {
			$modelName = $model->name;
			$conditions = array('model' => $modelName, 'display' => 1);
			$order = 'seq';
			$cfields = $this->_cfModel->find('all', compact('conditions', 'order'));
			if (!empty($cfields)) {
				$cfields[] = array(
					'CustomField' => array(
						'name' => 'id',
						'type' => 'hidden'
					)
				);
				$cfields[] = array(
					'CustomField' => array(
						'name' => Inflector::underscore($modelName) . '_id',
						'type' => 'hidden'
					)
				);
				$this->controller->params['_customFields'] = array($modelName => $cfields);
			}
		}
	}
}
?>
