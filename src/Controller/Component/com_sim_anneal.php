<?php
/**
 * Simulated Annealing Component
 */
require_once(LIBS.DS.'component_object.php');
class ComSimAnnealComponent extends ComponentObject{
  var $controller = true;
  var $components = array('ComLimit', 'ComTime', 'ComMessage');
  var $params = array(
      'temp_init' => 1000000,
      'temp_final' => 0.01,
      'max_run' => 2000,
      'anneal_rate' => 0.999,
      'message' => false,
    );

  /**
   * find the neighbor solution of schedule table
   */
  function neighbor(&$userShifts) {

    foreach ($userShifts as $uid => $singleUserShifts) {
      $length = count($singleUserShifts);
      for ($i = 1; $i<=3; $i++) :
        $elementIndex = rand(1, $length);
        $this->swapAttendance($userShifts, $uid, $elementIndex);
      endfor;
    }
  }

  /**
   * one step move
   * @param array $userShifts
   */
  function annealMove(&$userShifts) {
    static $uidIndex = null;
    static $userCount = -1;
    static $dayCount = -1;

    /**
     * in the annealing process, user shifts should remain unchanged
     * otherwise, the data will be wrong
     */
    if (is_null($uidIndex)) {
      $uidIndex = array_keys($userShifts);
    }
    if ($userCount === -1) {
      $userCount = count($userShifts);
    }
    if ($dayCount === -1) {
      $dayCount = count($userShifts[$uidIndex[0]]);
    }
    $swappeddUid = $uidIndex[rand(1, $userCount) - 1];
    $swappedDay = rand(1, $dayCount);

    $this->swapAttendance($userShifts, $swappeddUid, $swappedDay);
  }

  /**
   * swap the attendance status
   *   attend <=> not attend
   */
  function swapAttendance(&$userShifts, $userId = null, $elemIndex = null) {
    $userShifts[$userId][$elemIndex] = ($userShifts[$userId][$elemIndex] == 0) ?
     1 : 0;
  }

  function setSimAnnealParams($params=null) {
    if (empty($params)) {
      die('Wrong usage! ' . __FUNCTION__ . ' should supply correct params.');
    }
    foreach ($params as $key => $value) {
      /**
       * make sure only set supplied param, others remain
       */
      $this->params[$key] = $value;
    }
  }

  /**
   * start simulated annealing
   */
  function run($state = null, $year = null, $month = null) {
    $t0 = $this->params['temp_init'];
    $te = $this->params['temp_final'];
    $maxRun = $this->params['max_run'];
    $tn = $t0;  // new temperature
    $decay = $this->params['anneal_rate'];
    $count = 0;
    $stateChanged = false;
    $energyOld = -1;

    while (($tn > $te) and ($count < $maxRun)) {
      if ($energyOld == -1) {
        $energyOld = $this->energy($state, $year, $month);
        $initEnergy = $energyOld;
        $newState = $state;
      } elseif ($stateChanged) {
        $energyOld = $energyNew;
      } else {
        $newState = $state;
      }

      $this->annealMove($newState);
      $energyNew = $this->energy($newState, $year, $month);

      $energyDiff = $energyNew - $energyOld;
      if ($energyDiff <= 0) {
        // accept
        $state = $newState;
        $stateChanged = true;
      } else {
        // take probability
        //$prob = 1 - ($energyDiff / $tn);
        $prob = exp(-($energyDiff / $tn));
        $chance = rand(1, 1000) / 1000;
        if ($chance < $prob) {
          // accept
          $state = $newState;
          $stateChanged = true;
        } else {
          // reject
          $stateChanged = false;
        }
      }
      $tn = $decay * $tn;
      $count++;
    }
    DEBUG($count);
    DEBUG("ie:" . $initEnergy);
    DEBUG("fe:" . $energyOld);

    return $state;
  }

  function energy(&$state, $yr = null, $mon = null) {
    /**
     * find the energy of current state
     */
    static $wh = 100; // hard constraint weight
    static $ws = 1;  // soft constraint weight
    static $demand = array(
      1 => 2,
      2 => 4,
      3 => 2,
    );
    static $dayOfMonth = 0;
    if ($dayOfMonth == 0) {
      $dayOfMonth= $this->ComTime->getMonthDays($yr, $mon);
    }
    if ($this->params['message']) {
      $this->ComMessage->openMessage();
    }
    
    $energy = 
      //$this->ComLimit->interval($state, $dayOfMonth) * $wh
       $this->ComLimit->continuousWork($state, 7, $dayOfMonth) * $wh
      + $this->ComLimit->continuousHoliday($state, 4, $dayOfMonth) * $wh
      + $this->ComLimit->peopleRequest($state, $demand, $dayOfMonth) * $wh
      + $this->ComLimit->oneShiftInOneDay($state, $dayOfMonth) * $wh
      + $this->ComLimit->weeklyWorkingHour($state, $yr, $mon, $dayOfMonth) * $wh
      + $this->ComLimit->changeShift($state, $dayOfMonth) * $ws
      //+ $this->ComLimit->rate($state, 0.2, 1, $dayOfMonth) * $ws // night shift
      //+ $this->ComLimit->rate($state, 0.2, 3, $dayOfMonth) * $ws // evening shift
      + $this->ComLimit->twoHoliday($state, $yr, $mon, $dayOfMonth) * $ws
      + $this->ComLimit->breakInHoliday($state, $yr, $mon, $dayOfMonth) * $ws;
      ;
      
    return $energy;
  }
}
?>
