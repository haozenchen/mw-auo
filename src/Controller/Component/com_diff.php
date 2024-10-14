<?php 

/**
 * $Id: com_diff.php,v 1.1.2.1.4.2 2024/05/16 03:50:23 ashinjuang Exp $
 * $Author: ashinjuang $
 * $Date: 2024/05/16 03:50:23 $
 */
/* 
 source idea from 
 Paul's Simple Diff Algorithm v 0.1
 (C) Paul Butler 2007 <http://www.paulbutler.org/>
 original source is licensed under zlib/libpng license.
 */
/**
 * @copyright  Copyright 2007-2013, Fonsen Technology Ltd. Corp.
 */
require_once(LIBS.DS.'component_object.php');
class ComDiffComponent extends ComponentObject {
	var $addedLines = array();

	/**
	 * diff two file (in array)
	 * @param array $old
	 * @param array $new
	 */
	function diff($old = null,  $new = null) {
		$table = array();
		$maxlen = 0;
		foreach ($old as $oidx => $oval) {
			// look into old file (as array)
			$nkeys = array_keys($new, $oval);	// search a line (from old file) in new file
			foreach ($nkeys as $nidx) {
				$table[$oidx][$nidx] = isset($table[$oidx - 1][$nidx - 1]) ? $table[$oidx - 1][$nidx - 1] + 1 : 1;
				if ($table[$oidx][$nidx] > $maxlen) {
					$maxlen = $table[$oidx][$nidx];
					$omax = $oidx + 1 - $maxlen;
					$nmax = $nidx + 1 - $maxlen;
				}
			}
		}

		if ($maxlen == 0) {
			$this->addedLines = $new;	// we need this
			return array(array('d' => $old, 'i' => $new));
		}

		return array_merge(
			$this->diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
			array_slice($new, $nmax, $maxlen),
			$this->diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
	}

	/**
	 * append file to another
	 * @param string $resultFile
	 * @param string $fileToAppend
	 */
	function appendFile($resultFile = null,  $fileToAppend = null) {
		$this->appendContent($resultFile, file_get_contents($fileToAppend));
	}

	/**
	 * append content to a file
	 * @param string $resultFile
	 * @param array $content
	 */
	function appendContent($resultFile = null,  $content = '') {
		if (strpos($content, "\r\n")) {
			$content = "\r\n" . $content;
		} else {
			$content = "\n" . $content;
		}
		file_put_contents($resultFile, $content, FILE_APPEND);
	}
}
?>