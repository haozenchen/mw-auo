<?php
require_once(LIBS.DS.'component_object.php');
class SimplyCalendarComponent extends ComponentObject {
	var $name = 'SimplyCalendarComponent';
	var $components = array('ComTime','ComCalendar');
	var $controller = true;
	
	function startup(&$controller) {
		$this->controller = &$controller;
	}		
	
	function do_calendar($yrmon = null){	
		$weekDays=array('日','一','二','三','四','五','六');

		$this->controller->set('weekDays',$weekDays);
		if(!empty($this->controller->data['Holiday'])) {
			$lastDay=$this->ComTime->getMonthDays($this->controller->data['Holiday']['year'], $this->controller->data['Holiday']['month']);
			$year  =  $this->controller->data['Holiday']['year'];
			$month = $this->controller->data['Holiday']['month'];
			$this->controller->set('year',$year);
			$this->controller->set('month',$month);
    	}else{
			$todayInfo = getdate();
			$todayYear = $todayInfo['year'];
			if(!empty($yrmon)) {
				$year = substr($yrmon, 0, 4);
				$month = substr($yrmon, 5, 2);
				if($month > 12) {
					$year=$year+1;
					$month = 1;
					if($year > $todayYear+2) { // year upper limit
						$month = 12;
						$year = $todayYear+2;
					}
				}
				if($month < 1) {
					$month = 12;
					$year = $year -1;
					if($year < $todayYear-1) { //year lower limit
						$year=$todayYear-1;
						$month = 1;
					}
				}
			}else{
				$todayInfo = getdate();
				$year = $todayInfo['year'];
				$month = $todayInfo['mon'];
			}
			$this->controller->data['Holiday']['year'] = $year;
			$this->controller->data['Holiday']['month'] = $month;
			$lastDay = $this->ComTime->getMonthDays($year, $month);
			$this->controller->set('year', $year);
			$this->controller->set('month', $month);
		}
		$monthView = $this->ComCalendar->getMonthView((int)$month, $year);
		$this->controller->set('monthView', $monthView);
		return $monthView;
	}

}
?>
