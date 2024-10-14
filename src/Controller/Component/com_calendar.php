<?php
// PHP Calendar Class Version 1.4 (5th March 2001)
//  
// Copyright David Wilkinson 2000 - 2001. All Rights reserved.
// 
// This software may be used, modified and distributed freely
// providing this copyright notice remains intact at the head 
// of the file.
//
// This software is freeware. The author accepts no liability for
// any loss or damages whatsoever incurred directly or indirectly 
// from the use of this script. The author of this software makes 
// no claims as to its fitness for any purpose whatsoever. If you 
// wish to use this software you should first satisfy yourself that 
// it meets your requirements.
//
// URL:   http://www.cascade.org.uk/software/php/calendar/
// Email: davidw@cascade.org.uk
// Construct a calendar to show the current month

/**
 * $Id: com_calendar.php,v 2.4.44.2 2024/05/16 03:52:40 ashinjuang Exp $
 * $Author: ashinjuang $
 * $Date: 2024/05/16 03:52:40 $
 * 
 * This component serves as a calendar provider in cakephp arch
 * Modified from David Wilkinson's Work, now all rights reserved
 * Followin program are not freeware. Above notice is provided
 *  to highlight the original author. 
 * @copyright Copyright 2007, Fonsen Technology Ltd. Corp.
 */
//vendor('lib_time');
App::import('vendor', 'lib_time');
require_once(LIBS.DS.'component_object.php');
class ComCalendarComponent extends ComponentObject{
    var $controller = true;
    
    /**
     * Get the array of strings used to label the days of the week. This array 
     *  contains seven elements, one for each day of the week. The first entry
     *  in this array represents Sunday.
     */
    function getDayNames()
    {
        return $this->dayNames;
    }
    

    /**
     * Set the array of strings used to label the days of the week. This array
     *  must contain seven elements, one for each day of the week. The first 
     *  entry in this array represents Sunday.
     */
    function setDayNames($names = null)
    {
        $this->dayNames = $names;
    }
    
    /**
     * Get the array of strings used to label the months of the year. This array
     *  contains twelve elements, one for each month of the year. The first 
     *  entry in this array represents January. 
    */
    function getMonthNames()
    {
        return $this->monthNames;
    }
    
    /**
     * Set the array of strings used to label the months of the year. This array
     *  must contain twelve
     *  elements, one for each month of the year. The first entry in this array represents January.
     */
    function setMonthNames($names = null)
    {
        $this->monthNames = $names;
    }
    
    
    
    /**
     * Gets the start day of the week. This is the day that appears in the first
     *  column of the calendar. Sunday = 0.
     */
    function getStartDay()
    {
        return $this->startDay;
    }
    
    /**
     * Sets the start day of the week. This is the day that appears in the first
     *  column of the calendar. Sunday = 0.
     */
    function setStartDay($day = null)
    {
        $this->startDay = $day;
    }
    
    
    /**
     * Gets the start month of the year. This is the month that appears first in 
     *  the year view. January = 1.
     */
    function getStartMonth()
    {
        return $this->startMonth;
    }
    
    /** 
     * Sets the start month of the year. This is the month that appears first in 
     *  the year view. January = 1.
     */
    function setStartMonth($month = null)
    {
        $this->startMonth = $month;
    }
    
    
    /**
     *  Return the URL to link to in order to display a calendar for a given 
     *   month/year.
     *  You must override this method if you want to activate the "forward" and 
     *   "back" feature of the calendar.
     *  Note: If you return an empty string from this function, no navigation 
     *   link will be displayed. This is the default behaviour.
     *  If the calendar is being displayed in "year" view, $month will be set to 
     *   zero.
     */
    function getCalendarLink($month = null,  $year = null)
    {
        return "";
    }
    
    /**
     *  Return the URL to link to  for a given date.
     *  You must override this method if you want to activate the date linking
     *  feature of the calendar.
        
     *  Note: If you return an empty string from this function, no navigation 
     *   link will be displayed. This is the default behaviour.
     */
    function getDateLink($day = null,  $month = null, $year = null)
    {
        return "";
    }


    /**
     *  Return the HTML for the current month
     */
    function getCurrentMonthView()
    {
        $d = getdate();
        return $this->getMonthView($d["mon"], $d["year"]);
    }
    

    /**
     *  Return the HTML for the current year
     */
    function getCurrentYearView()
    {
        $d = getdate();
        return $this->getYearView($d["year"]);
    }
    
    
    /**
     *  Return the HTML for a specified month
     */
    function getMonthView($month = null,  $year = null)
    {
        return $this->getMonthData($month, $year);
    }
    

    /*************************************************************
    
        The rest are private methods. No user-servicable parts inside.
        
        You shouldn't need to call any of these functions directly.
        
    ***************************************************************/


    /**
     *  Calculate the number of days in a month, taking into account leap years.
     */
    function getDaysInMonth($month = null,  $year = null)
    {
        return LibTime::getMonthDays($year, $month);
        /*
        @deprecated
        
        if ($month < 1 || $month > 12)
        {

            if ($month == 0) return $this->daysInMonth[11];
            else return 0;

        }

        $d = $this->daysInMonth[$month - 1];
   
        if ($month == 2)
        {
            // Check for leap year
            // Forget the 4000 rule, I doubt I'll be around then...
        
            if ($year%4 == 0)
            {
                if ($year%100 == 0)
                {
                    if ($year%400 == 0)
                    {
                        $d = 29;
                    }
                }
                else
                {
                    $d = 29;
                }
            }
        }
    
        return $d;
        */
    }
		
		function weekDate($start = null, $weekNum = null, $relative = null, $year= null){
			return LibTime::weekDate($start,$weekNum,$relative,$year);
		} 
		
		function getPlusDate($day = null,$mon = null,$year = null, $plus = null){
			$daysInMonth = $this->getDaysInMonth($mon, $year);
			$daysInLastMonth = $this->getDaysInMonth($mon -1, $year);
			if($mon==1) $daysInLastMonth = $this->getDaysInMonth(12, $year-1);
			$day += $plus;	
			if($day > $daysInMonth){
				$day=$day-$daysInMonth;
				if($mon==12){
					$mon='1';
					$year += 1;
				} 
				else $mon += 1;
			} 
			elseif($day < 1){
				$day=$day+$daysInLastMonth;
				if($mon==1){
					$mon='12';
					$year -= 1;
				} 
				else $mon -= 1;
			}
			
			$date=sprintf("%04d-%02d-%02d",$year,$mon,$day);
			return $date;
		}
		
		function getWeekData($day = null,$mon = null,$year = null, $plus = null){
			$daysInMonth = $this->getDaysInMonth($mon, $year);
			$daysInLastMonth = $this->getDaysInMonth($mon -1, $year);
			if($mon==1) $daysInLastMonth = $this->getDaysInMonth(12, $year-1);
			
			$timestamp=mktime(0,0,0,$mon,$day,$year);
			$date=getdate($timestamp);
			$min=$date['mday']-$date['wday'];
			$max=$date['mday']+(6-$date['wday']);
			$weekData = array();
			for($i=$min;$i<=$max;$i++):
				$mday=$i;
				$newYear=$date['year'];
				$newMon=$date['mon'];
				if($mday > $daysInMonth){
					$mday=$i-$daysInMonth;
					if($date['mon'] == 12){
						$newMon=1;
						$newYear=$date['year']+1;
					}	
					else $newMon=$date['mon']+1;
				}
				elseif($mday < 1){
					$mday=$i+$daysInLastMonth;
					if($date['mon']==1){
						$newMon=12;
						$newYear=$date['year']-1;
					}
					else $newMon=$date['mon']-1;
				} 
				$weekData[]=sprintf("%04d-%02d-%02d",$newYear,$newMon,$mday);
			endfor;
			return $weekData;	
		}
		
    /**
     *  Generate the HTML for a given month
     */

    function getMonthData($m = null,  $y = null, $showOtherDays = 1)
    {
        
      $a = $this->adjustDate($m, $y);
      $month = $a[0];
      $year  = $a[1];
        
      if ($month == 1) {
        $daysInlastMonth = $this->getDaysInMonth(12, $year - 1);
      } else {
        $daysInlastMonth = $this->getDaysInMonth($month - 1, $year);
      }
    	$daysInMonth = $this->getDaysInMonth($month, $year);
    	$date = localtime(mktime(12, 0, 0, $month, 1, $year), 1);
    	
    	$first = $date["tm_wday"];

	
      // We need to work out what date to start at so that the first appears in the correct column
    	$d = $this->startDay + 1 - $first;
    	while ($d > 1)
    	{
    	    $d -= 7;
    	}

        // Make sure we know when today is, so that we can use a different CSS style
             
        if ($month == 1) {
          $p_month = 12;
          $n_month = 2;
        }elseif ($month == 12) {
          $p_month = 11;
          $n_month = 1;
        }else{
          $p_month = $month - 1;
          $n_month = $month + 1;
        }
        	    
	    $j = 1; $dYear = $year;
    	while ($d <= $daysInMonth)
    	{

    	    for ($i = 0; $i < 7; $i++)
    	    {

            	$holiday = null;
              if ($d > 0 && $d <= $daysInMonth)
    	        {
    	            	$s[] = array ('days' => $d, 'year' => $year, 'month' => $month, 'holiday' => $holiday);
    	        }
    	        elseif ($d <= 0 && $d <= $daysInMonth && $showOtherDays > 0)
    	        {
    	            	if ($month == 1) {
                      $dYear = $year - 1;
                    } else {
                      $dYear = $year;
                    }
                    $s[] = array ('days' => $daysInlastMonth + $d , 'year' => $dYear, 'month' => $p_month, 'holiday' => $holiday);
    	        }
              elseif ($showOtherDays > 0)
              {
                      if ($month == 12) {
                        $dYear = $year + 1;
                      } else {
                        $dYear = $year;
                      }
                      $s[] = array ('days' => $j, 'year' => $dYear, 'month' => $n_month, 'holiday' => $holiday);
                    	$j++;
              }
              else
              {
                    	$s[] = array ('days' => "&nbsp;", 'year' => null, 'month' => null, 'holiday' => null);
              }
              $d++;
    	    }
    	}
    	return $s;
    }

    /**
     *  Adjust dates to allow months > 12 and < 0. Just adjust the years 
     *   appropriately.
     *   e.g. Month 14 of the year 2001 is actually month 2 of year 2002.
     */
    function adjustDate($month = null,  $year = null)
    {
        $a = array();  
        $a[0] = $month;
        $a[1] = $year;
        
        while ($a[0] > 12)
        {
            $a[0] -= 12;
            $a[1]++;
        }
        
        while ($a[0] <= 0)
        {
            $a[0] += 12;
            $a[1]--;
        }
        
        return $a;
    }

    /** 
     * The start day of the week. This is the day that appears in the first 
     *  column of the calendar. Sunday = 0.
    */
    var $startDay = 0;

    /**
     * The start month of the year. This is the month that appears in the first 
     *  slot of the calendar in the year view. January = 1.
     */
    var $startMonth = 1;

    /**
     * The labels to display for the days of the week. The first entry in this 
     *  array represents Sunday.
     */
    var $dayNames = array("S", "M", "T", "W", "T", "F", "S");
    
    /**
     *  The labels to display for the months of the year. The first entry in 
     *   this array represents January.
     */
    var $monthNames = array("January", "February", "March", "April", "May", "June",
                            "July", "August", "September", "October", "November", "December");
    
    /**
     * The number of days in each month. You're unlikely to want to change this.
     *  ..
     * The first entry in this array represents January.
     */
    var $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    
}

?>
