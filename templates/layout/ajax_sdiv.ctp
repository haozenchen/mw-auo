<?php
/**
 * $Id: ajax_sdiv.ctp,v 1.1 2022/07/11 02:02:41 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:41 $
 */
?>

<?php require 'ajax.ctp'; ?>
<?php
if (isset($subDivOp) and ($subDivOp != 'stay') and !empty($subDivId)) {
	extract($ajaxSubDivOptConfig);
	extract($mainDivParams);	// from $_ajaxSubDivOptConfig
	$admArray = @ife(isset($adm), array('adm' => $adm), array());
	$options = array(
		'update' => $id,
		'loading' => '',
		'url' => array_merge($params, array_merge(array('action' => $action), $admArray)),
	);
	if ($subDivOp != 'trigger') {
		$options['loading'] = "Element.hide('$subDivId');"; 
	} else {
		/**
		 * trigger another action in main div
		 */
		$options['loading'] = "$('loading').show();";
		$options['complete'] = "$('$subDivId').hide(); $('$showDivId').show(); $('loading').hide();";
	}
	echo '<script>' . $ajax->remoteFunction($options) . '</script>';
}
?>
