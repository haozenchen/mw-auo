<?php
// src/Service/SyncService.php
namespace App\Service;

use Cake\ORM\TableRegistry;
use Cake\Http\Client;
use Cake\Log\Log;
use Cake\Utility\Hash;
use Cake\I18n\FrozenDate;
use Exception;

class DeleteLog
{
	public function do_del(){
		date_default_timezone_set('Asia/Taipei'); // 設定為台北時區
		$SaasSettings = TableRegistry::getTableLocator()->get('SaasSettings');
        $daysToKeep = $SaasSettings->getSys('LogExpired');
        $daysToKeep = (!empty($daysToKeep) && is_numeric($daysToKeep))? $daysToKeep:0;
        $this->del_file($daysToKeep);
		$this->del_record($daysToKeep);
	}
	public function del_record($daysToKeep){
		$SyncLogs = TableRegistry::getTableLocator()->get('SyncLogs');
		$SyncRecords = TableRegistry::getTableLocator()->get('SyncRecords');
		$SaasLoginRecords = TableRegistry::getTableLocator()->get('SaasLoginRecords');

	    $dateThreshold = (new FrozenDate())->subDays($daysToKeep);

	    $deletedRows = $SyncRecords->deleteAll(['created <' => $dateThreshold]);
	    if ($deletedRows > 0) {
	    	echo "已刪除同步紀錄: " . __('成功刪除 {0} 筆資料。', $deletedRows) . "\n";
	    }

	    $deletedRows = $SyncLogs->deleteAll(['created <' => $dateThreshold]);
	    if ($deletedRows > 0) {
	    	echo "已刪除同步紀錄log: " . __('成功刪除 {0} 筆資料。', $deletedRows) . "\n";
	    }

	    $deletedRows = $SaasLoginRecords->deleteAll(['created <' => $dateThreshold]);
	    if ($deletedRows > 0) {
	    	echo "已刪除登入紀錄: " . __('成功刪除 {0} 筆資料。', $deletedRows) . "\n";
	    }
	}

	public function del_file($daysToKeep){
    	$dir = LOGS;
    	if (is_dir($dir)) {
            // 使用 scandir 取得資料夾內的所有檔案和資料夾
            $files = scandir($dir);
            foreach ($files as $file) {
                // 忽略 . 和 ..
                if ($file !== '.' && $file !== '..') {
                    $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                    // 如果是檔案，則刪除
                    if (is_file($filePath)) {
	                    // 獲取檔案的最後修改時間
	                    $fileModificationTime = filemtime($filePath);
	                    // 獲取當前時間
	                    $currentTime = time();
	                    // 計算檔案距今的天數
	                    $daysOld = ($currentTime - $fileModificationTime) / (60 * 60 * 24);

	                    // 如果檔案的天數大於設定的保留天數，則刪除
	                    if ($daysOld > $daysToKeep) {
	                        unlink($filePath);
	                        echo "已刪除檔案: " . $file . "\n";
	                    }
	                }
                }
            }
        } else {
            echo "指定的路徑不是一個資料夾。";
        }
    }
}

?>