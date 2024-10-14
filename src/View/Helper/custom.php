<?php 
/**
 * $Id: custom.php,v 1.1 2022/07/11 02:02:41 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:41 $
 */   
/*
 * Helper for custom fields
 * @author $Author: andyyang $
 * @version $Revision: 1.1 $
 */
/**
 * @copyright  Copyright 2007, Fonsen Technology Ltd. Corp.
 */ 
class CustomHelper extends Helper {
	var $initialized = false;
	var $helpers = array('Form', 'Html');
	var $cfields = array();
	var $__cfieldModel = null;

	function customized($modelName) {
		if (!empty($this->params['_customFields'][$modelName])) {
			return true;
		}
		return false;
	}

	/**
	 * return input fields html
	 * @param string $modelName
	 * @param array $inpParams
	 * @param array $params
	 * @param array $checkIndividual
	 * @return string
	 */
	function inputs($modelName, $inpParams = array(), $allParams = array(), $checkIndividual = false) {
		$params = $this->params;
		$out = '';
		$customModelName = 'Custom' . $modelName;
		foreach ($params['_customFields'][$modelName] as $cfield) {
			$fname = $customModelName . '.' . $cfield['CustomField']['name'];
			$type = $cfield['CustomField']['type'];
			if ($type == 'hidden') {
				// hidden fields, dont need decoration
				$out .= $this->Form->input($fname, compact('type'));
			} else {
				if($checkIndividual) {
					$individual = $cfield['CustomField']['individual'];
				} else {
					$individual = 1;
				}
			   if($individual) {
					$label = $cfield['CustomField']['label'];
					switch ($type) {
					case 'boolean':
						$size = $cfield['CustomField']['length'];
						$maxlength = $size;
						$between = '';
						$iparams = array_merge(compact('size', 'maxlength', 'label'), (array)$inpParams);
						break;
					case 'varchar':
						$size = $cfield['CustomField']['length'];
						$maxlength = $size;
						$between = __('：', true);
						$iparams = array_merge(compact('size', 'maxlength', 'label', 'between'), (array)$inpParams);
						break;
					case 'date':
						$size = 1;
						$maxlength = '';
						$between = __('：', true);
						$dateFormat = 'YMD';
						$monthNames = false;
						$empty = true;
						$iparams = array_merge(compact('size', 'label', 'between', 'dateFormat', 'monthNames', 'empty'), (array)$inpParams);
						break;
					}

					// support display decoration
					$out .= ife(empty($allParams['before']), '', @$allParams['before']) . $this->Form->input($fname, $iparams) . ife(empty($allParams['after']), '', @$allParams['after']);
				} else {
					$out .= $this->Form->hidden($fname);
					$out .= @$inpParams['before'];
					$out .= $cfield['CustomField']['label'] . '：';
					$out .= ife($type == 'boolean', ife($this->data[$customModelName][$cfield['CustomField']['name']], '是', '否'), $this->data[$customModelName][$cfield['CustomField']['name']]);
					$out .= @$inpParams['after'];
				}
			}
		}

		return $out;
	}
}
?>
