<?php
/**
 * $Id: com_limit.php,v 2.0.48.2 2024/05/16 03:48:19 ashinjuang Exp $
 * $Author: ashinjuang $
 * $Date: 2024/05/16 03:48:19 $
 */   
/**
 * Limit (constraints) on our schedules
 * This component works for constraint checking.
 * It also return the message after checking
 */
require_once(LIBS.DS.'component_object.php');
class ComLimitComponent extends ComponentObject {
  var $controller = true;
  var $components = array('ComScheduletable', 'ComTime', 'ComMessage');
  /**
   * list of limits
   */
  var $limits = array(
      1 => 'interval',
      2 => 'continuousWork',
      3 => 'continuousHoliday',
      4 => 'rate',
      5 => 'peopleRequest',
      6 => 'oneShiftInOneDay',
      7 => 'changeShift',
      8 => 'weeklyWorkingHour',
      9 => 'twoHoliday',
      10 => 'breakInHoliday',
    );
  var $limitComments = array(
      1 => 'Lower Limit on interval between user shifts',
      2 => 'Upper Limit on continuous working days',
      3 => 'Upper Limit on continuous holidays',
      4 => 'Lower Limit on specified shift occurring rate (3-shift only)',
      5 => 'Lower Limit on people request on shift',
      6 => 'Upper Limit on one shift for one day',
      7 => 'Minimum shift change frequency (3-shift only)',
      8 => 'Limit on weekly working hours',
      9 => 'Lower Limit on days off in weekend',
      10 => 'Lower Limit on total days off in weekend',
    );
  var $parmNames = array(
      'us' => 'userShifts',
      'yr' => 'year',
      'mo' => 'month',
      'dc' => 'dcount',
      'md' => 'maxday',
      'dm' => 'demand',
      'rt' => 'rate',
    );
  
  function doCheck($selectedLimits = null,  $v = null) {
    foreach ($selectedLimits as $singleLimit) {
      switch ($singleLimit) {
        case 'breakInHoliday':
        case 'twoHoliday':
        case 'weeklyWorkingHour':
          $this->$singleLimit($v['userShifts'], $v['year'], $v['month'], $v['maxday']);
          break;
        case 'changeShift':
        case 'interval':
        case 'oneShiftInOneDay':
          $this->$singleLimit($v['userShifts'], $v['maxday']);
          break;
        case 'continuousHoliday':
          $dcount = 4; // use default for now
        case 'continuousWork':
          if (! isset($dcount)) {
            $dcount = 7; // use default for now
          }
          $this->$singleLimit($v['userShifts'], $dcount, $v['maxday']);
          break;
        case 'peopleRequest':
          $this->$singleLimit($v['userShifts'], $v['demand'], $v['maxday']);
          break;
        case 'rate':
          foreach ($v['shift_rate'] as $shift => $rate) {
            $this->$singleLimit($v['userShifts'], $rate, $shift, $v['maxday']);
          }
          break;
      }
    }
  }
  
  /**
   * limit on continuous holiday
   * @param mixed $userShifts all user shifts, the nurse roster
   * @param int $dcount max continuous holiday allowed
   * @param int $maxday total days in period
   * @return int summation of deviation
   */                 
  function continuousHoliday($userShifts = null, $dcount = null, $maxday = null) {
    $sumDeviation=0; 
    foreach ($userShifts as $userId => $userShift ){
      $total_sum=0; 
      for ($i=1; $i <= $maxday-$dcount+1; $i++){    
        for ($day=$i; $day <= $i+$dcount-1; $day++ ){
          $sumAttendance = 0;        
          for ($j=0; $j<=2; $j++){
            $sumAttendance += $this->ComScheduletable->attendance($userShifts[$userId],3*$day-$j);                 
          }            
          $total_sum += $sumAttendance;          
        }  
        if ($total_sum < 1){    // 至少一天可以上班        
          $sumDeviation ++;
          $this->ComMessage->addMessage('User id:' . $userId . 'has continuous holiday over:' . $dcount . ' started from day:' . $i . '.');
        }    
      }  
    }
    return $sumDeviation;
  }
  
  /**
   * limit on shift occuring rate
   * @param mixed $userShifts all user shifts, the nurse roster
   * @param int $rate min shift rate required
   * @param int $shift shift number in day, ex: 1, 2, 3
   * @param int $maxday total days in period
   * @return int summation of deviation
   */
  function rate($userShifts = null, $rate = null, $shift = null, $maxday= null) {
    $sumShiftAttendance = 0;
    $adduserTotalAttendance = 0;
    $sumDeviation = 0;
    foreach ($userShifts as $userId => $userShift ){
      for ($day=1; $day <= $maxday; $day++){
        $shiftAttendance = $this->ComScheduletable->attendance($userShifts[$userId],3*$day-(3 - $shift));
        $sumShiftAttendance += $shiftAttendance; //大夜
        $userTotalAttendance=0;  //重新計算三個班的值
        for ($j=0; $j<=2; $j++){
          $userTotalAttendance += $this->ComScheduletable->attendance($userShift,3*$day-$j);                                        
        }      
        $userTotalAttendance+=$shiftAttendance;
        $adduserTotalAttendance += $userTotalAttendance;    
      }       
      $shiftRequireAttendance = ceil($adduserTotalAttendance*$rate);  
      if ($sumShiftAttendance < $shiftRequireAttendance){               
        $sumDeviation += $shiftRequireAttendance - $sumShiftAttendance;
        $this->ComMessage->addMessage('The shift:' . $shift . ' rate of user id:' . $userId . ' is not reaching lower limit:' . $rate . '.');          
      }       
    }
    return $sumDeviation;
  }

  /**
   * limit on human resource request by shift
   * @param mixed $userShifts all user shifts, the nurse roster
   * @param int $demand data of human resource request by shift
   * @param int $maxday total days in period
   * @return int summation of deviation
   */
  function peopleRequest($userShifts = null, $demand = null, $maxday = null) {
    $sumDeviation=0;
    for ($j=1;$j<=$maxday;$j++){
      $sumAttendance=0;
      foreach ($userShifts as $userId => $userShift ){        
        if ($this->ComScheduletable->attendance($userShift,$j)>0 ){      
          $sumAttendance += 1;         
        }
      }
      $hrNeeded = $this->hrReq($j,$demand);
      if ( $sumAttendance < $hrNeeded) { 
        $sumDeviation += $hrNeeded - $sumAttendance;
        $this->ComMessage->addMessage('Human Resource of day:' . $j . ' is not reaching requirement:' . $hrNeeded . ' on shift:' . $j . '.');
      }
    }
    return $sumDeviation;
  }
  
  /**
   * find human resource request by given demand data
   * @param mixed $TotalShift all user shifts, the nurse roster
   * @param int $demand data of human resource request by shift
   * @return int resource request number
   */   
  function hrReq ($shiftIndex = null, $demand = null){ //所有班次
    $dayShiftIndex=fmod($shiftIndex,3);  
    if ($dayShiftIndex==0){
      $dayShiftIndex = 3;
    }
    return $demand[$dayShiftIndex];
  }

  /**
   * limit on continuous working days
   * @param mixed $userShifts all user shifts, the nurse roster
   * @param int $dcount max continuous working allowed, plus 1. ex: 6 max, dcount is 7
   * @param int $maxday total days in period
   * @return int summation of deviation
   */  
  function continuousWork($userShifts = null, $dcount = null, $maxday = null) {//連續上班不超n天
    $sumDeviation=0;
    foreach ($userShifts as $userId => $userShift ){
      for ($i=1; $i <= $maxday-$dcount+1; $i++){    
        $sumAttendance = 0;
        for ($day=$i; $day <= $i+$dcount-1; $day++ ){      
          for ($j=0; $j<=2; $j++){
            $sumAttendance += $this->ComScheduletable->attendance($userShifts[$userId],3*$day-$j);                 
          }
        }
        if ($sumAttendance > ($dcount - 1)) {
          $sumDeviation += 1;
          $this->ComMessage->addMessage('Continuous working days of user id:' . $userId . ' are over ' . ($dcount - 1) . ' starting on day:' . $i .'.');
        } 
      }  
    }
    return $sumDeviation;
  }
  
  /**
   * limit on shifts interval for user
   * for now, it's 2 (so shift in line is 3)   
   * @param mixed $userShifts all user shifts, the nurse roster
   * @param int $maxday total days in period
   * @return int summation of deviation
   */  
  function interval($userShifts = null, $maxDay = null){ //連續三班不連續
    $sumDeviation = 0;
    $shiftsInLine = 3;   
    foreach ($userShifts as $userId => $userShift ){
      for ($i=1; $i <= ((3*$maxDay) - ($shiftsInLine-1)); $i++ ) {
      // i: shift number in period        
        $sumAttendance = 0;
        for ($j = 0; $j <= ($shiftsInLine-1); $j++) {
          $sumAttendance += $this->ComScheduletable->attendance($userShifts[$userId], $i + $j);
        }
        if($sumAttendance > 1){        
          $sumDeviation += ($sumAttendance - 1);
          $this->ComMessage->addMessage('The interval of user id:' . $userId . ' on shift:' . $i . ' has interval less than 2 shifts.');        
        }
      }         
    } 
    return $sumDeviation;
  } 
  
  /**
   * limit of one shift, one day  
   * @param mixed $userShifts all user shifts, the nurse roster
   * @param int $max total days in period
   * @return int summation of deviation
   */
  function oneShiftInOneDay($userShifts = null, $max = null){
    $sumDeviation=0;
    foreach ($userShifts as $userId => $userShift){
      for ($day=1; $day <= $max; $day++){
        $sumAttendance=0;
        for ($j=0; $j<=2; $j++){
          $sumAttendance += $this->ComScheduletable->attendance($userShift, 3*$day-$j);
        }
        if ($sumAttendance > 1){
          $sumDeviation += $sumAttendance-1; 
          $this->ComMessage->addMessage('The number of total shifts of user id:' . $userId . ' is over 1 on day:' . $day . '.');
        }    
      }
    }
    return  $sumDeviation;
  }

  /**
   * limit of shift changing frequency  
   * @param mixed $userShifts all user shifts, the nurse roster
   * @param int $max total days in period
   * @return int summation of deviation
   */
  function changeShift($userShifts = null, $max = null){ //換班頻率
    $sumDeviation=0;
    $sumAttendance[1]=0;
    $sumAttendance[2]=0;
    $sumAttendance[3]=0;
    foreach ( $userShifts as $userId => $userShift){
      for ($day=2;$day <= $max;$day++){
        for ($j=0; $j<=2; $j++){
          $day1=$this->ComScheduletable->attendance($userShift,3*($day-1)-$j );
          $day2=$this->ComScheduletable->attendance($userShift,3*$day-$j );
          $sumAttendance[1] += $day1;
          $sumAttendance[2] += $day2;
          $sumAttendance[3] += abs($day1-$day2); 
        }
        $sumDeviation +=  $sumAttendance[1]*$sumAttendance[2]*$sumAttendance[3];       
      }
    }
    return $sumDeviation;
  }

  /**
   * limit of weekly working shifts
   * @param mixed $userShifts all user shifts, the nurse roster
   * @param int $year
   * @param int $month         
   * @param int $max total days in period
   * @return int summation of deviation
   */  
  function weeklyWorkingHour($userShifts = null, $year = null, $month = null, $max= null){
    $sumDeviation = 0;

    foreach ($userShifts as $userId => $userShift){
      $sumWeekAttendance[1] = 0;
      $sumWeekAttendance[2] = 0;
      $sumWeekAttendance[3] = 0;
      $sumWeekAttendance[4] = 0;
      $sumWeekAttendance[5] = 0;
      $w = 1; //預設第一週
      
      for ($day=1; $day<=$max; $day++){
        $wd = $this->ComTime->getWeekDay($year,$month,$day);
        if (($day > 1) and ($wd == 0)){ //計算共幾週
          $w += 1;
        }
        
        $sumAttendance=0;
        for ($j=0; $j<=2; $j++){
          $sumAttendance += $this->ComScheduletable->attendance($userShift,3*$day-$j); 
        }
        if ( $sumAttendance > 0 ){
          $sumWeekAttendance[$w] += 1;
        }
      }      
      foreach ($sumWeekAttendance as $week => $weeklyAttendance){
        if ($weeklyAttendance > 5){
           $sumDeviation += $weeklyAttendance-5;
           $this->ComMessage->addMessage('Working Shifts of user id:' . $userId . ' is over 5 on week:' . $week . '.');
        }
      }
    }   
    return $sumDeviation;
  }

  /**
   * limit of weekend holiday
   * @param mixed $userShifts all user shifts, the nurse roster
   * @param int $year
   * @param int $month         
   * @param int $max total days in period
   * @return int summation of deviation
   */
  function twoHoliday($userShifts = null, $year = null, $month = null, $max= null){
    $sumDeviation=0;
    foreach ($userShifts as $userId => $userShift){    
      $weekCount=0;
      $sumWeek[1]=0;
      $sumWeek[2]=0;
      $sumWeek[3]=0;
      $sumWeek[4]=0;
      $sumWeek[5]=0;
      $w=1; //預設第一週
      for ($day=1; $day <= $max; $day++ ){   
        $wd = $this->ComTime->getWeekDay($year,$month,$day);
        if ($wd==0){ //計算共幾週
          $w+=1;
        }        
        $attendance=0;
        for ($j=0; $j<=2; $j++){
          $attendance += $this->ComScheduletable->attendance($userShift,3*$day-$j);
        }
        if ($wd=='6' & $attendance==0 ){
          $sumWeek[$w] += 1;
        }
        if ($wd=='0' & $attendance==0 ){
          $sumWeek[$w] += 1;
        }        
      }
      
      foreach ($sumWeek as $week){
        if ($week > 2){
          $weekCount += 1;
        }
      }
      if ( $weekCount < 1){
        $sumDeviation++;
        $this->ComMessage->addMessage('Two day weekend of user id:' . $userId . ' is not reaching 1.');
      }      
    }
    return $sumDeviation;
  }

  /**
   * limit of holiday count in weekend
   * @param mixed $userShifts all user shifts, the nurse roster
   * @param int $year
   * @param int $month         
   * @param int $max total days in period
   * @return int summation of deviation
   */
  function breakInHoliday($userShifts = null, $year = null, $month = null, $max= null){
    $sumWeek=0;
    $sumDeviation=0;
    foreach ($userShifts as $userId => $userShift){
      for ($day=1; $day <= $max; $day++ ){       
        $wd = $this->ComTime->getWeekDay($year,$month,$day);       
        $AttenDance=0;
        for ($j=0; $j<=2; $j++){
          $AttenDance += $this->ComScheduletable->attendance($userShift,3*$day-$j);
        }
        if ($wd=='6' & $AttenDance==0 ){
          $sumWeek += 1;
        } elseif ($wd=='0' & $AttenDance==0 ){
          $sumWeek += 1;
        }        
      }      
      if ( $sumWeek < 4){
        $sumDeviation += (4-$sumWeek);
        $this->ComMessage->addMessage('Holiday break of user id:' . $userId . 'is not reaching 4.');
      }      
    }
    return $sumDeviation;
  }
}
?>
