<?php
/**
 * $Id: hlp_string.php,v 1.1 2022/07/11 02:02:41 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:41 $
 */   
/**
 * help class for string
 * @copyright   Copyright 2007, Fonsen Technology Ltd. Corp.
 */
class HlpStringHelper extends Helper {

	var $helpers = array('Html', 'Session');

	/**
	 * split multibyte string into array that contains elements of specified length
	 * @param string $string
	 * @param int $len
	 * @return array
	 */
	function mbSplit($string, $len) {
		preg_match_all("/(.){1,$len}/u", $string, $arr);
		return $arr;
	}
}
?>
