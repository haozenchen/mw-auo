<?php
/**
 * $Id: hlp_inpval.php,v 1.1 2022/07/11 02:02:41 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:41 $
 */   
/**
 * help class for input value
 * @copyright   Copyright 2009, Fonsen Technology Ltd. Corp.
 */
class HlpInpvalHelper extends Helper {

	var $helpers = array('Html', 'Session');

	/**
	 * a small function that helps transfer data like array(1,3) to array(0,2),
	 *  or data like 3 to 2, useful for exam paper data or something similar
	 * @param mixed $data
	 * @param int $shift Default -1
	 * @return mixed
	 */
	function getValue($data, $shift = -1) {
		if (is_array($data)) {
			$result = array();
			foreach ($data as $row) {
				$result[] = (int)$row + $shift;
			}
		} else {
			$result = (int)$data + $shift;
		}
		return $result;
	}

	/**
	 * recursively generate html list
	 * @param array $data
	 * @param string $out
	 * @return string
	 */
	function __recursiveList($data, $out) {
		$nl = "\n";

		foreach ($data as $k => $v) {
			if (is_array($v)) {
				$out .= '<li>' . $k . '</li>' . $nl . '   ' . '<ul>' . $nl . $this->__recursiveList($v, $out) . $nl . '   </ul>' . $nl;
			} else {
				$out .= '      ' . '<li>' . "$k : $v" . '</li>' . $nl;
			}
		}
		return $out;
	}

	/**
	 * generate html list in tree layout
	 * @param array $data
	 * @return string
	 */
	function parseArrayToList($data) {
		$out = $this->__recursiveList($data, '');
		$out = '<ul>' . $out . '</ul>';
		return $out;
	}
}
?>
