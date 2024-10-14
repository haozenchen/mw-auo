<?php
/* SVN FILE: $Id: ajax.ctp,v 1.1 2022/07/11 02:02:41 andyyang Exp $ */

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
<?php echo $content_for_layout; ?>
<?php
  /* need clear flash */
  if (isset($hlpHtml)) :
    echo $hlpHtml->clearFlash();
  endif;
  /**
   * if box is just turning off, don't check message
   */     
  if (! (isset($box_off) and $box_off)) :
    if($session->check('Message.flash')):
      $session->flash();
    endif;
  endif;
?>
