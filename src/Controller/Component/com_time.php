<?php
/**
 * $Id: com_time.php,v 2.1.44.2 2024/05/16 03:37:14 ashinjuang Exp $
 * $Author: ashinjuang $
 * $Date: 2024/05/16 03:37:14 $
 */
/**
 * Time Component
 * @copyright Copyright 2007, Fonsen Technology Ltd. Corp.
 * @deprecated use LibTime instead
 */
/**
 * move to LibTime in vendor
 */
require_once(LIBS.DS.'component_object.php');
//vendor('lib_time');
App::import('vendor', 'lib_time');

class ComTimeComponent extends ComponentObject{
  var $controller = true;

  /**
   * get total days count of specified year, month
   * @param int $year
   * @param int $month
   * @return int days count
   * @deprecated use LibTime::getMonthDays instead
   */
  function getMonthDays($year = null, $month = null) {
    return LibTime::getMonthDays($year, $month);
  }

  /**
   * find the weekday of the first day of specified year and month
   * @param int $year
   * @param int $month
   * @param int $day
   * @return int
   * @deprecated use LibTime::getWeekDayOfMonthFirstDay instead
   */
  function getWeekDayOfMonthFirstDay($year = null,$month = null,$day = null){ //每天的星期
    return LibTime::getWeekDayOfMonthFirstDay($year,$month,$day);
  }

  /**
   * find the weekday of the first day of specified year and month
   * @param int $year
   * @param int $month
   * @param int $day
   * @return int
   * @deprecated use LibTime::getWeekDay instead
   */
  function getWeekDay($year = null,$month = null, $day = null){ //每天的星期
    return LibTime::getWeekDay($year,$month, $day);
  }

 /**
  *find the six month date of specified year ,month,day
   * @param int $nowYear
   * @param int $nowMonth
   * @param int $nowDay
   * @param int $stopDay
   * @return array
   * */

  function sixMonthOptions($nowYear = null,$nowMonth = null,$nowDay = null,$stopDay = null) {
    if ($nowDay > $stopDay) {
      $day = $nowMonth+2;
      for ($i = $nowMonth+2;$i<= $nowMonth+7; $i++ ) {
        if ($day > 12 ) {
          $day = $day - 12;
          $nowYear += 1;
        }
       // $date[$nowYear][$day] = $nowYear."/".$day;
        $date[$nowYear."/".$day] =  $nowYear."/".$day;
        $day ++;
      }
    } else {
      $day = $nowMonth+1;
      for($i =$nowMonth+1; $i<=$nowMonth+6; $i++ ) {
        if ($day > 12 ) {
          $day = $day - 12;
          $nowYear += 1;
      }
        $date[$nowYear."/".$day] =  $nowYear."/".$day;
        $day ++;
      }
    }
    return $date;
  }

}
?>
