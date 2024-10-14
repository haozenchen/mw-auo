<?php
/**
 *$Id: com_sumallowance.php,v 2.0.48.1 2023/10/26 09:26:13 ashinjuang Exp $
 *$Author: ashinjuang $
 *$Date: 2023/10/26 09:26:13 $
 *to amount the Allowance of everning or night; 
 * * @copyright Copyright 2007, Fonsen Technology Ltd. Corp.
 */
require_once(LIBS.DS.'component_object.php');
class ComSumallowanceComponent extends ComponentObject {
  var $controller = true;   
  
  function sumAllowance($count = null, $price = null){
    return $count*$price;
  }
}
?>
