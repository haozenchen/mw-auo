<?php
/**
 * $Id: com_message.php,v 2.0.48.1 2023/10/26 10:30:32 ashinjuang Exp $
 * $Author: ashinjuang $
 * $Date: 2023/10/26 10:30:32 $
 * Message Component
 * This component handles module internal message  
 */
require_once(LIBS.DS.'component_object.php');
class ComMessageComponent extends ComponentObject {
  var $msgs = array();
  var $doAddMessage = false;
  
  /**
   * add message for further processing
   * @param string $msg message from function   
   */     
  function addMessage($msg = null) {
    if ($this->doAddMessage) {
      $this->msgs[] = $msg;
    }
  }
  
  /**
   * read messages
   * @return array   
   */  
  function readMessages() {     
    return $this->msgs;
  }
  
  /**
   * open message processing
   */
  function openMessage() {
    $this->doAddMessage = true;
  }     
  
  /**
   * close message processing
   */
  function closeMessage() {
    $this->doAddMessage = false;
  }
}
?>
