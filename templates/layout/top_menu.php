<?php
/**
 * $Id: top_menu.php,v 1.1 2022/07/11 02:02:41 andyyang Exp $
 * $Author: andyyang $
 * $Date: 2022/07/11 02:02:41 $
 * @copyright Copyright 2007, Fonsen Technology Ltd. Corp.
 */
/**
 * top menu in array   
 */ 
$top_menu2 = array(
  'hr' => array(
    'name' => '人事管理',
    'items' => array(
      '管理部門' => EMMA_ROOT . 'Departments',
      '管理人員' => EMMA_ROOT . 'Users',
      '管理人員類別' => EMMA_ROOT . 'UserTypes',
    )
  ),
  'sal' => array(
    'name' => '薪資津貼',
    'items' => array(
      '設定排班津貼' => EMMA_ROOT . 'Allowances',
      '設定值勤津貼' => EMMA_ROOT . 'DutyAllowances',
    )
  ),
  'sch' => array(
    'name' => '排班管理',
    'items' => array(
      '進行排班作業' => EMMA_ROOT . 'Schedules/add',
      '管理班表' => EMMA_ROOT . 'Schedules',
      //'設定檢查規則' => EMMA_ROOT . 'Limitgroups',
      '加退時數管理' => EMMA_ROOT . 'ScheduleAddHours',
    )
  ),
  'usraff' => array(
    'name' => '申請/查詢',
    'items' => array(
      '檢視當月班表' => EMMA_ROOT . 'Schedules/view_current',
      '預約班表' => EMMA_ROOT . 'BookingSchedules/add',
      '檢視次月預約班表' => EMMA_ROOT . 'BookingSchedules/view_current',
      '瀏覽全部班表' => EMMA_ROOT . 'Schedules/browse',
      '瀏覽全部預約班表' => EMMA_ROOT . 'BookingSchedules/browse',
      '瀏覽部門/員工姓名' => EMMA_ROOT . 'Users/browse',
      '瀏覽假日行事曆' => EMMA_ROOT . 'Holidays/browse',
    )
  ),
  'bb' => array(
    'name' => '公告管理',
    'items' => array(
      '設定班表預約注意事項公告' => EMMA_ROOT . 'Announcements/booking_schedule',
      '設定班表發佈公告事項' => EMMA_ROOT . 'Announcements/schedule',
    )
  ),
  'basic' => array(
    'name' => '基本資料管理',
    'items' => array(
      '設定假日行事曆' => EMMA_ROOT . 'Holidays',
      '設定循環假日' => EMMA_ROOT . 'Holidays/edit',
      '管理假別' => EMMA_ROOT . 'LeaveTypes',
      '管理院內班別' => EMMA_ROOT . 'Shifts',
      '管理院內值班別' => EMMA_ROOT . 'Duties',
      '設定部門班別' => EMMA_ROOT . 'DepartmentShifts',
    )
  ),
  'priv' => array(
    'name' => '權限管理',
    'items' => array(
      '管理帳號' => EMMA_ROOT . 'Accounts',
      '管理權限群組' => EMMA_ROOT . 'Pgroups',
      '設定群組權限' => EMMA_ROOT . 'Permissions',
    )
  ),
);

?>
