<?php
/**
 * $Id: com_loader.php,v 2.2.34.1 2023/10/26 09:26:13 ashinjuang Exp $
 * $Author: ashinjuang $
 * $Date: 2023/10/26 09:26:13 $
 *
 * @copyright Fonsen Technology Ltd. Corp.
 */    
/**
 * loader component, which helps load model, component in controller action
 * which may help save memory for controller
 * usage in controller action:
 *  1. add 'ComLoader' in var $components
 *  2. $this->ComLoader->loadModel or $this->ComLoader->loadComponent
 *  3. done. use model or component as usual way
 */
namespace App\Controller\Component;
use Cake\Controller\Component;
class ComLoaderComponent extends Component {
	var $name = 'ComLoaderComponent';
	var $params = null;
	var $components = array('Session');
	var $controller = null;
	var $message = null;

	/**
	 * initialize
	 */
	// function initialize(&$controller) {
	// 	$this->controller = &$controller;
	// 	return true;
	// }

	public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->getController();
    }

	/**
	 * start up
	 */
	function startup() {
		return true;
	}

	/**
	 * load model
	 * @param string $name
	 * @param array $initParams
	 */
	function loadModel($name = null,  $initParams = null) {
		if (isset($this->controller->$name)) {
			$this->message = 'Name exists in controller: '.$name;
			return false;
		}
		App::import('Model', $name);
		$this->controller->$name = new $name($initParams);
	}

	/**
	 * load component
	 * @param string $name
	 * @param array $initParams
	 */
	function loadComponent($name = null,  $object = null, $initParams = null) {
		if (isset($this->controller->$name)) {
			$this->message = 'Name exists in controller: '.$name;
			return false;
		}
		App::import('Component', $name);
		$className = $name.'Component';
		$component = new $className($initParams);

		/**
		 * do some setup
		 * component inside component must be initliazed too
		 */
		if (isset($component->components)) {
			$this->controller->Component->_loadComponents($component);	// load recursively
		}
		if (method_exists($component, 'initialize')) {
			$component->initialize($this->controller);
		}
		if (method_exists($component, 'startup')) {
			$component->startup($this->controller);
		}

		/**
		 * now loaded
		 */
		$this->controller->$name = $component;
	}
}
