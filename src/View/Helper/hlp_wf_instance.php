<?php
/**
 * $Id: hlp_wf_instance.php,v 1.1 2022/07/11 02:02:41 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:41 $
 */   
/**
 * help class for workflow instance
 * @copyright Copyright 2007, Fonsen Technology Ltd. Corp.
 */
define('NL', "\n"); 
class HlpWfInstanceHelper extends Helper {

	var $helpers = array('Html', 'Paginator', 'othAuth', 'Session');

	/**
	 * helper function that provides form content display
	 * @param mixed $data WfInstance.context or unserialized (array)
	 * @param bool $batch Default true. If set to true, data that set for batch will be shown
	 * @return string
	 */
	function formContent($data, $batch = true) {
		if (is_string($data)) {
			$data = unserialize($data);
			$formCont = $data['formContent'];
		} elseif (is_array($data)) {
			$formCont = $data;
		} else {
			die('Not Supported format');
		}
		require CONFIGS . 'wf_instance_context.php';
		$type = $formCont['_type'];
		unset($formCont['_type']);
		$out = '';
		foreach ($formCont as $k => $v) {
			if (isset($contextConfs[$type][$k]['batch']) and ($contextConfs[$type][$k]['batch'] === true)) {
				$out .= $contextConfs[$type][$k]['desc'] . "：<font color='#996600'>" . $v;
			}
		}
		return $out;
	}

}
?>
