<?php
require_once(LIBS.DS.'component_object.php');
class ComScheduletableComponent extends ComponentObject {
  var $controller=true;

  /**
   * read user attendance data, by day
   * obsolete
   */
  function attendanceByDay($singleUserShift = null, $shiftNumberInPeriod = null){ //換算三班
    $shiftNumberInOneDay = fmod($shiftNumberInPeriod, 3);
    if ($shiftNumberInOneDay == 0) {
      $shiftNumberInOneDay = 3;
    }
    $shiftMask = array(
      'D'=>2,
      'E'=>1,
      'N'=>4,
      'O'=>0,
      );
    $day = (int)(($shiftNumberInPeriod-1)/3) + 1;
    $val = $shiftMask[$singleUserShift[$day]] & pow(2, 3-$shiftNumberInOneDay);
    if ($val > 0){   
      return 1;
    } else {   
      return 0;
    }    
  }
  
  /**
   * read user attendance data, by shift number
   */
  function attendance($singleUserShift = null, $shiftNumberInPeriod = null) {
    return ($singleUserShift[$shiftNumberInPeriod]);
  }

  /**
   * @obsolete  
   * get initial schedule table
   * we give two array output here for view to print
   */
  function getScheduleTableOld($users = null,  $shifts = null, $dayOfMonth = null) {
      $userShifts = array();
      $u = array();
      $userCount = 1;
      $shift = array(
        'N' => array(1, 0, 0),
        'D' => array(0, 1, 0),
        'E' => array(0, 0, 1),
        'O' => array(0, 0, 0),
      );
      foreach ($users as $userId => $name) {      
        $userShifts[$userId]['name'] = $name;
        $userShifts[$userId]['count'] = $dayOfMonth;
        for ($i = 1; $i <= $dayOfMonth; $i++) {
          $c1 = 3 * $i - 2;
          $c2 = 3 * $i - 1;
          $c3 = 3 * $i;
          /*if ($userCount <= 4) {
            $userShifts[$userId][$i] = 'O';
            list($u[$userId][$c1], $u[$userId][$c2], $u[$userId][$c3]) = $shift['O'];
          } elseif ($userCount <= 7) {
            $userShifts[$userId][$i] = 'O';
            list($u[$userId][$c1], $u[$userId][$c2], $u[$userId][$c3]) = $shift['O'];
          } elseif ($userCount <= 10) {
            $userShifts[$userId][$i] = 'O';
            list($u[$userId][$c1], $u[$userId][$c2], $u[$userId][$c3]) = $shift['O'];
          } else {
            $userShifts[$userId][$i] = 'O';
            list($u[$userId][$c1], $u[$userId][$c2], $u[$userId][$c3]) = $shift['O'];
          }*/
          $userShifts[$userId][$i] = 'O';
          list($u[$userId][$c1], $u[$userId][$c2], $u[$userId][$c3]) = $shift['O'];
       }  
       $userCount++;
    }
    return array($userShifts, $u);
  }

  /**
   * get initial schedule table
   * we give two array output here for view to print
   */
  function getScheduleTable($users = null,  $shifts = null, $dayOfMonth = null) {
      $userShifts = array();
      $u = array();
      $shift = array(
        'N' => array(1, 0, 0),
        'D' => array(0, 1, 0),
        'E' => array(0, 0, 1),
        'O' => array(0, 0, 0),
      );
      $shiftCount = 4;
      $shiftIndex = array_keys($shift);
      foreach ($users as $userId => $name) {      
        $userShifts[$userId]['name'] = $name;
        $userShifts[$userId]['count'] = $dayOfMonth;
        for ($i = 1; $i <= $dayOfMonth; $i++) {
          $c1 = 3 * $i - 2;
          $c2 = 3 * $i - 1;
          $c3 = 3 * $i;
          $givenShift = $shiftIndex[rand(1,$shiftCount) - 1];
          $userShifts[$userId][$i] = $givenShift;
          list($u[$userId][$c1], $u[$userId][$c2], $u[$userId][$c3]) = $shift[$givenShift];
        }
      }
      return array($userShifts, $u);
  }
  
  /**
   * get schedule table from form data
   * the data should have key like 'id_date_uid'   
   * @param mixed $formData the form data
   * @return array schedule table data
   */      
  function getScheduleTableFromForm($formData = null) {
    $userShifts = array();
    foreach ($formData as $k => $v) {
      if ((substr($k, 0, 3) === 'id_') and (substr($k, 5, 1) == '_')) {
        list(, $date, $uid) = preg_split('/_/', $k); /* parse key */
        $userShifts[$uid][(int)$date] = $v;
      }
    }
    return $userShifts;
  }
  
  /**
   * transform schedule table from shift code format to 3-shift 0-1 format
   * the shift code only support 3-shift, ex: D,E,N,O   
   * @param array $userShifts the user shifts in shift code format
   * @return array transformed schedule table
   */           
  function transToZOTable($userShifts = null) {
    $shift = array(
      'N' => array(1, 0, 0),
      'D' => array(0, 1, 0),
      'E' => array(0, 0, 1),
      'O' => array(0, 0, 0),
    );
    $u = array(); /* new schedule table */
    
    foreach ($userShifts as $userId => $ushift) {
      for ($i = 1; $i <= count($ushift); $i++) {
        $c1 = 3 * $i - 2;
        $c2 = 3 * $i - 1;
        $c3 = 3 * $i;
        list($u[$userId][$c1], $u[$userId][$c2], $u[$userId][$c3]) = $shift[$ushift[$i]];
      }
    }
    return $u;
  }
  
  function transToSCTable($userShifts = null) {
  }
}
?>
