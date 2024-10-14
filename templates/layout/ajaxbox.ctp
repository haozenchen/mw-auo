<?php
/* SVN FILE: $Id: ajaxbox.ctp,v 1.1 2022/07/11 02:02:41 andyyang Exp $ */


/**
 *
 * 
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc. 
 *			1785 E. Sahara Avenue, Suite 490-204
 *			Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource 
 * @copyright		Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake.libs.view.templates.layouts
 * @since			CakePHP(tm) v 0.10.0.1076
 * @version			$Revision: 1.1 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2022/07/11 02:02:41 $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>

<?php require 'ajax.ctp'; ?>
<?php
if (! isset($ajax_off_page)) {
	$ajax_off_page = 'listing';
}
if (! isset($ajax_listing_div)) {
	$ajax_listing_div = 'listing';
}
if (! isset($ajax_action_params)) {
	$ajax_action_params = array();
}
if (! isset($ajax_req_options)) {
	$ajax_req_options = array();
}
if (isset($box_off) and $box_off and !empty($box_id)) {
	$options = array(
		'update' => $ajax_listing_div,
		'url' => array_merge(array('action' => $ajax_off_page), (array)$ajax_action_params),
		'loading'  => "Element.hide('$box_id'); Element.show('loading');",
		'complete' => "Element.hide('loading'); Element.hide('transbtm');", 
	);
	$options = array_merge($options, $ajax_req_options);
	echo '<script type="text/javascript">' . $ajax->remoteFunction($options) . '</script>';
}
?>
