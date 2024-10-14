<?php
/**
 * $Id: hlp_time.php,v 1.1 2022/07/11 02:02:41 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:41 $
 */   
/**
 * help class for time
 * @copyright   Copyright 2007, Fonsen Technology Ltd. Corp.
 */  
class HlpTimeHelper extends Helper {
	/**
	 * generate year options
	 * @param int $backwards number of years to count backwards from this year
	 * @param int $forward number of years to count forward from this year
	 * @return array year options
	 */     
	function yearOptions($backwards = 3, $forward = 3) {
		$now = getdate();
		$thisYear = $now['year'];

		for ($i = ($thisYear - $backwards); $i <= ($thisYear + $forward); $i++) {
			$options[$i] = $i."年";
		}
		return $options;
	}

	/**
	 * generate month options
	 * @return array month options (1..12)   
	 */
	function monthOptions() {
		for ($i = 1; $i <= 12; $i++) {
			$options[$i] = $i."月";
		}
		return $options;
	}

	function dayOptions() {
		for ($i = 1; $i <= 31; $i++) {
			$options[$i] = $i."日";
		}
		return $options;
	}

	function lastTweleveMonth($nowYear){  
		for($y=$nowYear-1; $y<=$nowYear ;$y++ ){
			for($m=1; $m<=12 ;$m++){

				$result[$y."/".$m] = $y."年". $m ."月";  
			}		
		}	
		return $result;
	}


	function sixMonthOptions($nowYear,$nowMonth,$nowDay,$stopDay) {
		if ($nowDay > $stopDay) {     
			$day = $nowMonth+2;
			for ($i = $nowMonth+2;$i<= $nowMonth+7; $i++ ) {
				if ($day > 12 ) {
					$day = $day - 12;         
					$nowYear += 1;          
				}
				// $date[$nowYear][$day] = $nowYear."/".$day;
				$date[$nowYear."/".$day] =  $nowYear.'年'.$day.'月';               
				$day ++;       
			}    
		} else {
			$day = $nowMonth+1;
			for($i =$nowMonth+1; $i<=$nowMonth+6; $i++ ) {       
				if ($day > 12 ) {
					$day = $day - 12;         
					$nowYear += 1;          
				}       
				$date[$nowYear."/".$day] =  $nowYear.'年'.$day.'月';         
				$day ++;        
			}      
		}   
		return $date;  
	} 

	/**
	 * generate year-month options in array('2009-01' => '2009-01') format for input()
	 * @param int $bwd Month count backward from now
	 * @param int $bwd Month count forward from now
	 * @param year-month $startYrMon
	 * @param year-month $endYrMon 
	 * @return array
	 */
	function yearMonthOptions($bwd = 0, $fwd = 3, $startYrMon = null, $endYrMon = null) {
		$reverse = false;
		if (!empty($startYrMon) && !empty($endYrMon)) {
			if ($startYrMon > $endYrMon) {
				$reverse = true;
			}
			$startMonth = (substr($startYrMon, 0, 4) * 12) + substr($startYrMon, 5, 2);
			$endMonth = (substr($endYrMon, 0, 4) * 12) + substr($endYrMon, 5, 2);
		} else {
			$dateInfo = getdate();
			extract($dateInfo);
			if ($bwd > $fwd) {
				$reverse = true;
			}
			$current = $year * 12 + $mon;
			$startMonth = $current + $bwd;
			$endMonth = $current + $fwd;

		}
		
		if ($reverse === true) {
			for ($i = $startMonth; $i >= $endMonth; $i--) {
				$nm = fmod($i, 12);
				$ny = floor($i / 12);
				if ($nm == 0) {
					$ny--;
					$nm = 12;
				}
				$ym = sprintf("%04d-%02d", $ny, $nm);
				$options[$ym] = $ym;
			}
		} else {
			for ($i = $startMonth; $i <= $endMonth; $i++) {
				$nm = fmod($i, 12);
				$ny = floor($i / 12);
				if ($nm == 0) {
					$ny--;
					$nm = 12;
				}
				$ym = sprintf("%04d-%02d", $ny, $nm);
				$options[$ym] = $ym;
			}
		}
		return $options;
	}


}
?>
