<?php
// src/Service/SyncService.php
namespace App\Service;

use Cake\ORM\TableRegistry;
use Cake\Http\Client;
use Cake\Log\Log;
use Cake\Utility\Hash;
use Exception;

class SyncService
{
	private $syncId;  // 宣告變數
	private $auo_token;
    private $test = false;
    private $timeout = 3600;

    public function doSync($data=array(), $emUid=false){
    	date_default_timezone_set('Asia/Taipei'); // 設定為台北時區
        if(!empty($data)){
        	$SyncRecords = TableRegistry::getTableLocator()->get('SyncRecords');
        	$ip = false;
        	if(!empty($emUid)){
        		$ip = $SyncRecords->getClientIP();
        	}
            $record = array(
                'saas_admin_id' => $emUid,
                'ip_address_ip' => $ip,
                'status' => 'waiting',
                'created' => date('Y-m-d H:i:s')
            );
            $this->save_sync_record($record);
            $syncJobs = $this->syncJob($data);
            if(!empty($syncJobs)){
                $user_update = $syncJobs['trans_count']['user'];
                $department_update = $syncJobs['trans_count']['dep'];
                $this->save_sync_record(array('id' => $this->syncId, 'user_update' => $user_update, 'department_update' => $department_update));
                list($chk_dep, $chk_user) = $this->diffReference($syncJobs['trans_count']);
                if(empty($chk_dep) || empty($chk_user)){
                    $status = 'error';
                }else{
                    $err_datas = array();
                    $err_datas = $this->do_sync_api($syncJobs);
                    // 統整所有同步錯誤訊息
                    $this->sendMail($err_datas, true);
                    $status = 'success';
                    if((!empty($user_update) && $this->countFuc($err_datas['user']) ==  $user_update) || (!empty($department_update) &&  $this->countFuc($err_datas['dep']) ==  $department_update)){
                        $status = 'error';
                    }else if(!empty($err_datas['user']) || !empty($err_datas['dep'])){
                        $status = 'warning';
                    }
                }
                $this->save_sync_record(array('id' => $this->syncId, 'status' => $status));
            }else{
                $status = 'error';
                $this->save_sync_record(array('id' => $this->syncId, 'status' => $status));
            }
            $result = 'ok';
        }else{
            $result = '無欲執行AUO資料';
        }

        return $result;
    }

    public function syncJob($data){
        $this->getAUOToken();
        $syncJobs = array(
            'add_dep' => array(), //部門新增
            'upd_dep_name' => array(), //部門更名
            'upd_dep' => array(), //部門批量更新
            'add_user' => array(), //人員新進
            //------------在職人員異動------------------
            'lv_user' => array(), //人員離退
            'ud_lv_user' => array(), //人員留停
            're_user' => array(), //人員復職
            'chg_user' => array(), //人員調派
            'upd_user' => array(), //人員批量更新
            //------------在職人員異動------------------
            'upd_dep_dir' => array(), //部門主管更新
            'del_user' => array(), //員工刪除
            'del_dep' => array(), //部門裁撤
            'trans_count' => array(
                'dep'=> 0,
                'user'=> 0
            )
        );

        list($auoDids, $auo_deps, $err) = $this->AuoDep();
        $this->save_sync_record(array('id' => $this->syncId, 'department_total' => $this->countFuc($auo_deps)));
        if(!empty($err)){return false;}

        list($auoUids, $auo_user1s, $err) = $this->AuoUser($auo_deps);
        $this->save_sync_record(array('id' => $this->syncId, 'user_total' => $this->countFuc($auoUids)));
        if(!empty($err)){return false;}

        list($auoUid2s, $auo_user2s, $err) = $this->AuoUser2();
        $this->save_sync_record(array('id' => $this->syncId, 'user_total' => $this->countFuc($auoUids+$auoUid2s)));
        if(!empty($err)){return false;}

        list($auoAids, $auo_appovers, $err) = $this->AuoAppover();
        $auoAllUids = $auoUids + $auoAids + $auoUid2s;
        $this->save_sync_record(array('id' => $this->syncId, 'user_total' => $this->countFuc($auoAllUids)));
        if(!empty($err)){return false;}

        //------------------------------------------------------------
        if(!empty($data['dep'])){
            list($fsDids, $fs_deps, $err) = $this->FemasDep();
            if(!empty($err)){return false;}

            $syncJobs = $this->add_dep($syncJobs, $auo_deps, $auoDids, $fsDids, $auoAllUids);
            $syncJobs = $this->upd_dep($syncJobs, $auo_deps, $fs_deps, $auoDids, $fsDids, $auoAllUids);
            $syncJobs = $this->del_dep($syncJobs, $auoDids, $fsDids);
        }
        //------------------------------------------------------------
        $fsAllUids = array();
        if(!empty($data['appover'])){
            list($fsAids, $fs_appovers) = $this->FemasUser('AUHQ'); //鋒形簽核主管資料
            $fsAllUids += $fsAids;
        }
        if(!empty($data['user']) || !empty($data['user2']) || !empty($data['edu']) || !empty($data['exp']) || !empty($data['appover'])){
            list($fsUids, $fs_users) = $this->FemasUser('AUVN', $auo_deps); //鋒形一般員工資料
            $fsAllUids += $fsUids;
        }
        $uids = array();
        //------------------------------------------------------------
        if(!empty($data['appover'])){
            $params = array('auo_user1s'=>$auo_appovers, 'auoAllUids'=>$auoAllUids);
            $auo_appovers = $this->AuoUserIntegrate($params, true);
            $syncJobs = $this->add_user($syncJobs, $auo_appovers, $auoAids, $fsAllUids, true);
            $syncJobs = $this->upd_user($syncJobs, $auo_appovers, $fs_appovers, $auoAids, $fsAllUids, true);
        }
        //------------------------------------------------------------
        if(!empty($data['user'])){
            $params['auoAllUids'] = $auoAllUids;
            $params['auo_user1s'] = $auo_user1s;
            $uids += (!empty($auoUids)? $auoUids:array());
        }

        if(!empty($data['user2'])){
            $params['auo_user2s'] = $auo_user2s;
            $uids += (!empty($auoUid2s)? $auoUid2s:array());
        }

        if(!empty($data['edu'])){
            list($eduUids, $auo_edu) = $this->AuoEdu();
            if(!empty($auo_edu)){
                $params['auo_edu'] = $auo_edu;
                $uids += $eduUids;
            }
        }
        if(!empty($data['exp'])){
            list($expUids, $auo_exp) =  $this->AuoExp();
            if(!empty($auo_exp)){
                $params['auo_exp'] = $auo_exp;
                $uids += $expUids;
            }
        }

        if(!empty($data['user']) || !empty($data['user2']) || !empty($data['edu']) || !empty($data['exp'])){
            $params['uids'] = $uids;
            $auo_users = $this->AuoUserIntegrate($params); //統整AUO員工資料
        }

        if(!empty($data['user'])){
            $syncJobs = $this->add_user($syncJobs, $auo_users, $auoUids, $fsAllUids);
        }
        //------------------------------------------------------------
        if(!empty($data['user']) || !empty($data['user2']) || !empty($data['edu']) || !empty($data['exp'])){
            $syncJobs = $this->upd_user($syncJobs, $auo_users, $fs_users, $uids, $fsAllUids);
        }
        //------------------------------------------------------------
        if(!empty($data['user']) || !empty($data['appover'])){
            $syncJobs = $this->del_user($syncJobs, $auoAllUids, $fsAllUids);
        }
        //------------------------------------------------------------
        // Log::error(var_export($syncJobs, true));
        return $syncJobs;
    }

    public function do_sync_api($syncJobs){
        $err_datas = array('dep' => array(), 'user' => array());
        $syncAPI = array(
            'add_dep' => 'su_department_update.json',
            'upd_dep_name' => 'su_department_order.json',
            'upd_dep' => 'su_department_update.json',
            'add_user' => 'su_new_user.json',
            'lv_user' => 'su_leaving_users.json',
            'ud_lv_user' => 'su_leaving_unpaid_users.json',
            're_user' => 'su_reinstate_users.json',
            'chg_user' => 'su_personnel_orders.json',
            'upd_user' => 'su_user_update.json',
            'upd_dep_dir' => 'su_department_update.json',
            'del_user' => 'su_user_update.json',
            'del_dep' => 'su_department_update.json.json'
        );

        $syncName = array(
            'add_dep' => '部門新增',
            'upd_dep_name' => '部門更名',
            'upd_dep' => '部門批量更新',
            'add_user' => '員工新進作業',
            'lv_user' => '員工離退作業',
            'ud_lv_user' => '員工留停作業',
            're_user' => '員工回任/復職作業',
            'chg_user' => '員工調派作業',
            'upd_user' => '員工批量更新',
            'upd_dep_dir' => '部門主管更新',
            'del_user' => '員工刪除',
            'del_dep' => '部門刪除'
        );


        foreach ($syncJobs as $key => $job) {
            if(!empty($syncAPI[$key]) && !empty($job)){
                list($res ,$err_msg) = $this->requestFemas($syncAPI[$key], ['datas' => $job]);
                $total_count = $this->countFuc($job);
                Log::error(var_export(array($syncAPI[$key] ,$job), true));
                if(empty($err_msg)){
                    $res_err = $res['err_datas'];
                    if(!empty($res_err)){
                        $err_identify = Hash::combine($job,'{n}.sn','{n}');
                        foreach ($res_err as $err) {
                            $err_val = '';
                            if(!empty($err['msg'])){
                                preg_match("/\((.*?)\)/", (string)$err['msg'], $matches);
                                if (!empty($matches[1])) {
                                    $field = $matches[1];
                                    if(isset($err['data'])){
                                        $err_val = $err['data'][$field];
                                    }else{
                                        $err_val = $err_identify[$err['sn']][$field];
                                    }
                                }
                            }
                            switch ($key) {
                                case 'add_dep':
                                case 'upd_dep':
                                case 'del_dep':
                                case 'upd_dep_dir':
                                case 'upd_dep_name':
                                    $err_datas['dep'][$err['sn']][] = array('action' => $syncName[$key], 'msg' => $err['msg'], 'value' => $err_val);
                                    break;
                                default:
                                    $err_datas['user'][$err['sn']][] = array('action' => $syncName[$key], 'msg' => $err['msg'], 'value' => $err_val);
                                    break;
                            }
                        }
                    }
                    if(!empty($res_err)){
                        $error_count = $this->countFuc($res_err);
                        if($total_count - $error_count > 0){
                            $status = 'warning';
                        }else{
                            $status = 'error';
                        }
                    }else{
                        $error_count = 0;
                        $status = 'success';
                    }
                }else{
                    $error_count = $total_count;
                    $status = 'error';
                }

                $log_msg = array('response' => $res, 'err_msg' => $err_msg);
                $record = array(
                    'sync_records_id' => $this->syncId,
                    'action' => $syncName[$key].'('.$syncAPI[$key].')',
                    'type' => 'sync',
                    'total_count' => $total_count,
                    'success_count' => $total_count - $error_count,
                    'error_count' => $error_count,
                    'status' => $status ,
                    'msg' => json_encode($log_msg,JSON_UNESCAPED_UNICODE),
                    'created' => date('Y-m-d H:i:s')
                );

                $this->save_sync_log($record);
            }
        }
        return $err_datas;
    }

    public function diffReference($tran_count){
        $SaasSettings = TableRegistry::getTableLocator()->get('SaasSettings');
        $userRef = $SaasSettings->getSys('UserDiffReference');
        $userRef = (!empty($userRef) && is_numeric($userRef))? $userRef:0;

        $depRef = $SaasSettings->getSys('DepartmentDiffReference');
        $depRef = (!empty($depRef) && is_numeric($depRef))? $depRef:0;

        $diff_user = $tran_count['user'];
        $diff_dep = $tran_count['dep'];

        $chk_dep = true;
        $chk_user = true;
        if(!empty($diff_dep)){
            $record = array('sync_records_id' => $this->syncId, 'api_host' => 'Femas', 'action' => '部門異動閥值檢查', 'total_count' => $diff_user, 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
            if($diff_dep > $depRef){
                $record['status'] = 'error';
                $record['error_count'] = $diff_dep;
                $record['msg'] = '異動數大於閥值設定：'.$depRef;
                $chk_dep = false;
            }else{
                $record['success_count'] = $diff_dep;
            }
            $this->save_sync_log($record, true);
        }

        if(!empty($diff_user)){
            $record = array('sync_records_id' => $this->syncId, 'api_host' => 'Femas', 'action' => '員工異動閥值檢查', 'total_count' => $diff_user, 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
            if($diff_user > $userRef){
                $record['status'] = 'error';
                $record['error_count'] = $diff_user;
                $record['msg'] = '異動數大於閥值設定：'.$userRef;
                $chk_user = false;
            }else{
                $record['success_count'] = $diff_user;
            }
            $this->save_sync_log($record, true);
        }

        return array($chk_dep, $chk_user);
    }



    public function genHtml($data) {
        $html = '';

        if(!empty($data['dep'])){
            $html .= "<table>";
            $html .= "<tr><th>序</th><th>部門編號ORGID</th><th>執行動作</th><th>錯誤訊息</th><th>值</th></tr>";
            $k = 1;
            foreach ($data['dep'] as $depId => $records) {
                foreach ($records as $record) {
                    $html .= "<tr>";
                    $html .= "<td>{$k}</td>";
                    $html .= "<td>{$depId}</td>";
                    $html .= "<td>{$record['action']}</td>";
                    $html .= "<td>{$record['msg']}</td>";
                    $html .= "<td>{$record['value']}</td>";
                    $html .= "</tr>";
                    $k+=1;
                }
            }
            $html .= "</table>";
        }

        if(!empty($data['user'])){
            $html .= "<table>";
            $html .= "<tr><th>序</th><th>員編EMPNO</th><th>執行動作</th><th>錯誤訊息</th><th>值</th></tr>";
            $k = 1;
            foreach ($data['user'] as $userId => $records) {
                foreach ($records as $record) {
                    $html .= "<tr>";
                    $html .= "<td>{$k}</td>";
                    $html .= "<td>{$userId}</td>";
                    $html .= "<td>{$record['action']}</td>";
                    $html .= "<td>{$record['msg']}</td>";
                    $html .= "<td>{$record['value']}</td>";
                    $html .= "</tr>";
                    $k+=1;
                }
            }
            $html .= "</table>";
        }

        return $html;
    }

    public function genHtml2($data) {
        $html = '';
        $k = 1;
        if(!empty($data)){
            $type = (!empty($data['type']))? '資料同步':'資料撈取';
            $api_host = (!empty($api_host))? 'AUO':'Femas';
            $typeText = array('scan' => '資料撈取', 'sync' => '資料同步');
            $html .= "<h4 style='margin-top:50px'>檢測到異常錯誤，同步執行中斷</h4>";
            $html .= "<table>";
            $html .= "<tr><th>序</th><th>執行類型</th><th>API類別</th><th>執行動作</th><th>錯誤訊息</th></tr>";
            $html .= "<tr>";
            $html .= "<td>{$k}</td>";
            $html .= "<td>{$type}</td>";
            $html .= "<td>{$api_host}</td>";
            $html .= "<td>{$data['action']}</td>";
            $html .= "<td>{$data['msg']}</td>";
            $html .= "</tr>";
            $html .= "</table>";
        }
        return $html;
    }

    public function generateTable($data, $do_sync = false) {
        if(!empty($do_sync)){
            $html = $this->genHtml($data);
        }else{
            $html = $this->genHtml2($data);
        }

        $SyncRecords = TableRegistry::getTableLocator()->get('SyncRecords');
        if(!empty($this->syncId)){
            $syncRecord = $SyncRecords->get($this->syncId, [
                'contain' => [],
            ]);
        }

        $dep_total = !empty($syncRecord['department_total'])? $syncRecord['department_total']:0;
        $dep_update = !empty($syncRecord['department_update'])? $syncRecord['department_update']:0;
        $dep_err = $this->countFuc($data['dep']);
        $user_total = !empty($syncRecord['user_total'])? $syncRecord['user_total']:0;
        $user_update = !empty($syncRecord['user_update'])? $syncRecord['user_update']:0;
        $user_err = $this->countFuc($data['user']);

        $emailContent = "
        <html>
        <head>
            <meta charset='UTF-8' />
            <title>使用者資料表格</title>
            <style>
                table{border-collapse: collapse;border: 1px solid #ddd;margin-bottom:20px;font-size:14px;}
                table.scan th{background-color: #f5dcdc}
                th{background-color: beige;}
                th,td{border: 1px solid #ddd; padding: 4px 8px;}
                *{font-family: Arial, sans-serif;}
            </style>
        </head>
        <body>
            <h3>同步執行結果</h3>
            <table class='scan'>
                <tr><th>類型</th><th>AUO掃描總數</th><th>異動數</th><th>失敗數</th></tr>
                <tr>
                    <td>部門</td>
                    <td>{$dep_total}</td>
                    <td>{$dep_update}</td>
                    <td>{$dep_err}</td>
                </tr>
                <tr>
                    <td>員工</td>
                    <td>{$user_total}</td>
                    <td>{$user_update}</td>
                    <td>{$user_err}</td>
                </tr>
            </table>
            " . $html . "
        </body>
        </html>";

        return $emailContent;
    }

    public function sendMail($data, $do_sync){
        $SaasSettings = TableRegistry::getTableLocator()->get('SaasSettings');
        $host = $SaasSettings->getSys('mail_host');
        $mailCode = $SaasSettings->getSys('email_code');
        $recipients = $SaasSettings->getSys('email_address');
        $subject = 'test';
        $emailContent = $this->generateTable($data, $do_sync);

        $record = array('sync_records_id' => $this->syncId, 'api_host' => 'AUO', 'action' => 'AUI IDS(ManualSend_07)', 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
        Log::error($emailContent);
        $http = new Client();
        $headers = [
          'Content-Type' => 'application/xml'
        ];

        $body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
           <soapenv:Header/>
           <soapenv:Body>
              <tem:ManualSend_07>
                 <!--Optional:-->
                 <tem:strMailCode>'.$mailCode.'</tem:strMailCode>
                 <!--Optional:-->
                 <tem:strRecipients>'.$recipients.'</tem:strRecipients>
                 <!--Optional:-->
                 <tem:strSubject>'.$subject.'</tem:strSubject>
                 <!--Optional:-->
                 <tem:strBody>'.$emailContent.'</tem:strBody>
              </tem:ManualSend_07>
           </soapenv:Body>
        </soapenv:Envelope>';

        try {
            $response = $http->post($host, $body, ['headers' => $headers]);
            if ($response->isOk()) {
                $status = 'success';
            }else{
                $err_msg = 'statusCode：'. $response->getStatusCode();
            }
        } catch (Exception $e) {
            $err_msg = "發生錯誤：" . $e->getMessage();
        }
    }

    public function add_dep($syncJobs, $auo_deps, $auoDids, $fsDids, $auoAllUids){
        $addDepIds = array_diff($auoDids, $fsDids);
        $add_dep = array();
        $upd_dep = array();
        $upd_dep_dir = array();
        foreach ($addDepIds as $id) {
            $add_dep[$id]['sn'] = $id;
            $add_dep[$id]['name'] = $auo_deps[$id]['org_id'].' '.$auo_deps[$id]['name'];
            $add_dep[$id]['brief_name'] = $auo_deps[$id]['brief_name'];
            $add_dep[$id]['start_date'] = $auo_deps[$id]['start_date'];
            if(!empty($auo_deps[$id]['superior_dp_sn'])){
                $upd_dep[$id]['sn'] = $id;
                $upd_dep[$id]['superior_dp_sn'] = $auo_deps[$id]['superior_dp_sn'];
            }
            if(!empty($auo_deps[$id]['manager_sn']) && in_array($auo_deps[$id]['manager_sn'], $auoAllUids)){
                $upd_dep_dir[$id]['sn'] = $id;
                $upd_dep_dir[$id]['manager_sn'] = $auo_deps[$id]['manager_sn'];
            }
        }
        $syncJobs['add_dep'] = array_merge($syncJobs['add_dep'], array_values($add_dep));
        $syncJobs['upd_dep'] = array_merge($syncJobs['upd_dep'], array_values($upd_dep));
        $syncJobs['upd_dep_dir'] = array_merge($syncJobs['upd_dep_dir'], array_values($upd_dep_dir));
        $syncJobs['trans_count']['dep'] += $this->countFuc($add_dep);
        return $syncJobs;
    }

    public function upd_dep($syncJobs, $auo_deps, $fs_deps, $auoDids, $fsDids, $auoAllUids){
        $intDepIds = array_intersect_assoc($fsDids, $auoDids);
        $upd_dep = array();
        $upd_dep_name = array();
        $upd_dep_dir = array();
        foreach ($intDepIds as $id) {
            $diff_dep = array();
            foreach ($auo_deps[$id] as $key => $value) {
                if($auo_deps[$id][$key] != $fs_deps[$id][$key]){
                    if($key == 'manager_sn'){
                        if(empty($value) || (!empty($value) && in_array($value, $auoAllUids))){
                            $diff_dep[$key] = $auo_deps[$id][$key];
                        }
                    }else{
                        $diff_dep[$key] = $auo_deps[$id][$key];
                    }
                }
            }
            if(!empty($diff_dep)){
                foreach ($diff_dep as $key => $value) {
                    switch ($key) {
                        case 'name':
                            //測試用，待部門更新完成要拿掉$upd_dep
                            $upd_dep_name[$id]['sn'] = $id;
                            $upd_dep_name[$id]['document_number'] = date('Y-m-d').' 部門更名';
                            $upd_dep_name[$id]['start_date'] = date('Y-m-d');
                            $upd_dep_name[$id]['effective_date'] = date('Y-m-d');
                            $upd_dep_name[$id]['update_dep'] = true;
                            $upd_dep_name[$id]['update_user'] = true;
                            $upd_dep_name[$id]['reason'] = '部門更名';
                            $upd_dep_name[$id]['remarks'] = '部門更名';
                            $upd_dep_name[$id]['name'] = $auo_deps[$id]['org_id'].' '.$value;
                            break;
                        case 'org_id':
                            $upd_dep[$id]['sn'] = $id;
                            $upd_dep[$id]['name'] = $value.' '.$auo_deps[$id]['name'];
                            break;
                        case 'manager_sn':
                            $upd_dep_dir[$id]['sn'] = $id;
                            $upd_dep_dir[$id]['manager_sn'] = $value;
                            break;
                        case 'superior_dp_sn':
                            $upd_dep[$id]['sn'] = $id;
                            $upd_dep[$id]['superior_dp_sn'] = $value;
                            break;
                        default:
                            $upd_dep[$id]['sn'] = $id;
                            $upd_dep[$id][$key] = $value;
                            break;
                    }
                }
                $syncJobs['trans_count']['dep'] += 1;
            }
        }
        $syncJobs['upd_dep'] = array_merge($syncJobs['upd_dep'], array_values($upd_dep));
        $syncJobs['upd_dep_name'] = array_values($upd_dep_name);
        $syncJobs['upd_dep_dir'] = array_merge($syncJobs['upd_dep_dir'], array_values($upd_dep_dir));
        return $syncJobs;
    }

    public function del_dep($syncJobs, $auoDids, $fsDids){
        $delDepIds = array_diff($fsDids, $auoDids);
        $del_dep = array();
        foreach ($delDepIds as $id) {
            if($id != 'AUHQ'){ //簽核主管部門排除
                $del_dep[$id]['sn'] = $id;
                $del_dep[$id]['deleted'] = true;
            }
        }
        $syncJobs['del_dep'] = array_values($del_dep);
        $syncJobs['trans_count']['dep'] += $this->countFuc($del_dep);
        return $syncJobs;
    }

    public function add_user($syncJobs, $auo_users, $auoUids, $fsUids, $appover = false){
        $addUserIds = array_diff($auoUids, $fsUids);
        $add_user = array();
        $upd_user = array();
        foreach ($addUserIds as $id) {
            $add_user[$id]['sn'] = $id;
            $add_user[$id]['base'] = $auo_users[$id]['base'];
            $add_user[$id]['lvday']['pass'] = true;
            $add_user[$id]['dependant']['pass'] = true;
            if(empty($appover)){
                $advanced_fields = array();
                foreach ($add_user[$id]['base']['advanced_fields'] as $code => $value) {
                    $advanced_fields[] = array(
                        'code' => $code,
                        'value' => $value
                    );
                }
                $add_user[$id]['base']['advanced_fields'] = $advanced_fields;
            }

            if(empty($appover) && !empty($auo_users[$id]['salary'])){//簽核主管新進，不需執行薪資步驟
                $salary = $auo_users[$id]['salary'];
                $salary['salary_group'] = '全體員工';
                $salary['reason'] = '新進';
            }else{
                $salary = array('pass' => true);
            }

            $add_user[$id]['salary'] = $salary;
            $add_user[$id]['insurance']['pass'] = true;
            $add_user[$id]['pension']['pass'] = true;
            if(!empty($upd_user[$id]['boss_sn'])){
                $upd_user[$id]['sn'] = $id;
                $upd_user[$id]['boss_sn'] = $auo_users[$id]['base']['boss_sn'];
            }
            unset($add_user[$id]['base']['boss_sn']);
        }
        $syncJobs['add_user'] = array_merge($syncJobs['add_user'], array_values($add_user));
        $syncJobs['upd_user'] = array_merge($syncJobs['upd_user'], array_values($upd_user));
        $syncJobs['trans_count']['user'] += $this->countFuc($add_user);
        return $syncJobs;
    }

    public function upd_user($syncJobs, $auo_users, $fs_users, $auoUids, $fsUids, $appover = false){
        $intUserIds = array_intersect_assoc($fsUids, $auoUids);
        $ing_fields = array('shift_type', 'holiday_calendar_name', 'card_number');//批次更新不處理欄位
        $lv_fields = array('leavedate');
        $chg_fields = array('department_name', 'user_title_name', 'user_type_name', 'user_grade');
        $array_fields = array('advanced_fields', 'military_service_record', 'education_record', 'work_experience');
        $lv_user = array();
        $re_user = array();
        $chg_user = array();
        $upd_user = array();
        /**
            規則說明：
            針對AUO與鋒形員工的欄位比對差異，針對部分欄位判斷預執行動作

            人員被判斷有離退or留停or復職，執行相對應作業，其餘欄位變動，皆透過「批次更新」處理，若同時有被判斷到調派，則調派欄位直接納入「批次更新」處理。
            人員被判斷調派，並且同時無被判斷離退or留停or復職，執行調派作業，其餘欄位變動，皆透過「批次更新」處理。
            針對有判斷調派、離退、留停、復職人員，會再進一步透過diff_chg()分析AUO、Femas異動紀錄，符合條件才被登記確定異動作業，若回傳False則透過「批次更新」處理。

            人員若有調派、離退、留停、復職、批次更新，同時有多個判斷，都只視為一筆異動，如：人員判斷有離退和批次更新，統計只會算1筆異動。
        */
        foreach ($intUserIds as $id) {
            foreach ($auo_users[$id]['base'] as $key => $value) {
                if(!in_array($key, $array_fields) && !in_array($key, $ing_fields)){
                    if(isset($fs_users[$id][$key]) && $value != $fs_users[$id][$key]){
                        if(!empty($appover)){
                            $upd_user[$id][$key] = $value;
                        }else{
                            if(in_array($key, $lv_fields)){
                                if(empty($value) && !empty($fs_users[$id][$key])){ //復職
                                    $re_user[$id][$key] = $value;
                                }else if(!empty($value) && empty($fs_users[$id][$key])){ //離退、留停
                                    $lv_user[$id][$key] = $value;
                                }else{
                                    $upd_user[$id][$key] = $value;
                                }
                            }elseif(isset($auo_users[$id]['base']['leavedate']) && empty($auo_users[$id]['base']['leavedate']) && in_array($key, $chg_fields)){ //調派
                                $chg_user[$id][$key] = $value;
                            }else{ //其餘欄位視為人員批量更新
                                $upd_user[$id][$key] = $value;
                            }
                        }
                    }
                }else{
                    switch ($key) {
                        case 'advanced_fields':
                            $fs_adv = $fs_users[$id][$key];
                            foreach ($value as $q => $qver) {
                                if(in_array($q, array_keys($fs_adv)) && $qver != $fs_adv[$q]){
                                    $upd_user[$id][$key][] = array('code' => $q, 'value' => $qver);
                                }
                            }
                            break;
                        case 'military_service_record':
                            $fs_mil = $fs_users[$id][$key];
                            if(!empty($fs_mil)){
                                $upd_mil = $this->diff_mil($fs_mil, $value);
                                if(!empty($upd_mil)){
                                    $upd_user[$id][$key] = $upd_mil;
                                }
                            }elseif(!empty($value)){
                                $upd_user[$id][$key] = $value;
                            }
                            break;
                        case 'education_record':
                            $fs_edu = $fs_users[$id][$key];
                            if(!empty($fs_edu)){
                                $upd_edu = $this->diff_edu($fs_edu, $value);
                                if(!empty($upd_edu)){
                                    $upd_user[$id][$key] = $upd_edu;
                                }
                            }elseif(!empty($value)){
                                $upd_user[$id][$key] = $value;
                            }
                            break;
                        case 'work_experience':
                            $fs_exp = $fs_users[$id][$key];
                            if(!empty($fs_exp)){
                                $upd_exp = $this->diff_exp($fs_exp, $value);
                                if(!empty($upd_exp)){
                                    $upd_user[$id][$key] = $upd_exp;
                                }
                            }elseif(!empty($value)){
                                $upd_user[$id][$key] = $value;
                            }
                            break;
                        default:
                            break;
                    }
                }
            }

            if(!empty($lv_user[$id]) || !empty($re_user[$id]) || !empty($chg_user[$id]) || !empty($upd_user[$id])){
                $syncJobs['trans_count']['user'] += 1; //同一個員工的人事異動、更新皆視為同一異動數
            }
            if(!empty($upd_user[$id])){
                $upd_user[$id]['sn'] = $id;
            }
        }
        if((!empty($lv_user) || !empty($re_user) || !empty($chg_user)) && empty($appover)){
            //當人員有離退、留停、復職、調派，查詢AUO是否有異動紀錄
            $chkUids = array_merge(array_keys($lv_user), array_keys($re_user), array_keys($chg_user));
            $auo_chgs = $this->AuoUserChange($chkUids);
            $fs_chgs = $this->FemasUserChange($chkUids);
            if(!empty($lv_user)){
                $LeaveUser = array();
                $UnpaidLeaveUser = array();
                foreach ($lv_user as $id => $user) {
                    if(empty($auo_chgs[$id])){//無異動紀錄，視為批量更新
                        $upd_user[$id]['sn'] = $id;
                        $upd_user[$id] = (!empty($upd_user[$id]))? array_merge($upd_user[$id], $user): $user;
                    }else{
                        $chg = $this->diff_chg($fs_chgs[$id], $auo_chgs[$id]);
                        if(!empty($chg) && $chg['action'] == 'LeaveUser'){
                            $LeaveUser[$id]['document_number'] = $chg['date'];
                            $LeaveUser[$id]['sn'] = $id;
                            $LeaveUser[$id]['start_date'] = $chg['date'];
                            $LeaveUser[$id]['type'] = 1;
                            $LeaveUser[$id]['reason'] = '其他';//待討論
                            $LeaveUser[$id]['remarks'] = $chg['reason'];

                            if(!empty($chg_user[$id])){
                                $upd_user[$id] = (!empty($upd_user[$id]))? array_merge($upd_user[$id], $chg_user[$id]): $chg_user[$id];
                                unset($chg_user[$id]);
                            }
                        }elseif(!empty($chg) && $chg['action'] == 'UnpaidLeaveUser'){
                            $UnpaidLeaveUser[$id]['document_number'] = $chg['date'];
                            $UnpaidLeaveUser[$id]['sn'] = $id;
                            $UnpaidLeaveUser[$id]['start_date'] = $chg['date'];
                            $UnpaidLeaveUser[$id]['estimated_end'] = date('Y-m-d', strtotime('+1 year', strtotime($chg['date'])));//待討論
                            $UnpaidLeaveUser[$id]['remarks'] = $chg['reason'];

                            if(!empty($chg_user[$id])){
                                $upd_user[$id] = (!empty($upd_user[$id]))? array_merge($upd_user[$id], $chg_user[$id]): $chg_user[$id];
                                unset($chg_user[$id]);
                            }
                        }else{
                            $upd_user[$id]['sn'] = $id;
                            $upd_user[$id] = (!empty($upd_user[$id]))? array_merge($upd_user[$id], $user): $user;
                        }
                    }
                }
                $syncJobs['lv_user'] = array_values($LeaveUser);
                $syncJobs['ud_lv_user'] = array_values($UnpaidLeaveUser);
            }

            if(!empty($re_user)){
                $Reinstate = array();
                foreach ($re_user as $id => $user) {
                    if(empty($auo_chgs[$id])){//無異動紀錄，視為批量更新
                        $upd_user[$id]['sn'] = $id;
                        $upd_user[$id] = (!empty($upd_user[$id]))? array_merge($upd_user[$id], $user): $user;
                    }else{
                        $chk = $this->diff_chg($fs_chgs[$id], $auo_chgs[$id]);
                        if(!empty($chg) && $chg['action'] == 'Reinstate'){
                            $Reinstate[$id]['document_number'] = $chg['date'];
                            $Reinstate[$id]['sn'] = $id;
                            $Reinstate[$id]['reinstate_date'] = $chg['date'];
                            $Reinstate[$id]['org_name'] = $auo_users[$id]['base']['org_name'];
                            $Reinstate[$id]['dept_name'] = $auo_users[$id]['base']['department_name'];
                            $Reinstate[$id]['user_type_name'] = $auo_users[$id]['base']['user_type_name'];
                            if(!empty($chg_user[$id])){
                                $upd_user[$id] = (!empty($upd_user[$id]))? array_merge($upd_user[$id], $chg_user[$id]): $chg_user[$id];
                                unset($chg_user[$id]);
                            }
                        }else{
                            $upd_user[$id]['sn'] = $id;
                            $upd_user[$id] = (!empty($upd_user[$id]))? array_merge($upd_user[$id], $user): $user;
                        }
                    }
                }
                $syncJobs['re_user'] = array_values($Reinstate);
            }

            if(!empty($chg_user)){
                $UserChange = array();
                foreach ($chg_user as $id => $user) {
                    if(empty($auo_chgs[$id])){//無異動紀錄，視為批量更新
                        $upd_user[$id]['sn'] = $id;
                        $upd_user[$id] = (!empty($upd_user[$id]))? array_merge($upd_user[$id], $user): $user;
                    }else{
                        $chg = $this->diff_chg($fs_chgs[$id], $auo_chgs[$id]);
                        if(!empty($chg) && $chg['action'] == 'UserChange'){
                            $UserChange[$id] = $user;
                            $UserChange[$id]['document_number'] = $chg['date'].' '.$id;
                            $UserChange[$id]['sn'] = $id;
                            $UserChange[$id]['effective_date'] = $chg['date'];
                            $UserChange[$id]['start_date'] = $chg['date'];
                            $UserChange[$id]['property'] = 1;
                            $UserChange[$id]['type'] = 1;
                            $UserChange[$id]['reason'] = $chg['reason'];;
                            $UserChange[$id]['user_type_name'] = $auo_users[$id]['base']['user_type_name'];
                            $UserChange[$id]['dept_name'] = $auo_users[$id]['base']['department_name'];
                            $UserChange[$id]['update_user'] = true;
                            // Log::error(var_export(array($id, $auo_users[$id]), true));
                        }else{
                            $upd_user[$id]['sn'] = $id;
                            $upd_user[$id] = (!empty($upd_user[$id]))? array_merge($upd_user[$id], $user): $user;
                        }
                    }
                }
                $syncJobs['chg_user'] = array_values($UserChange);
            }
        }
        $syncJobs['upd_user'] = array_merge($syncJobs['upd_user'], array_values($upd_user));
        return $syncJobs;
    }

    public function del_user($syncJobs, $auoUids, $fsUids){
        $delUserIds = array_diff($fsUids, $auoUids);
        $del_user = array();
        foreach ($delUserIds as $id) {
            $del_user[$id]['sn'] = $id;
            $del_user[$id]['deleted'] = true;
        }
        $syncJobs['del_user'] = array_merge($syncJobs['del_user'], array_values($del_user));
        $syncJobs['trans_count']['user'] += $this->countFuc($del_user);

        return $syncJobs;
    }

    public function diff_chg($fs_user, $auo_user){
        $auo_chg = array();
        $fs_chg = array();
        /*
            規則說明：
            比對該人員於AUO和Femas的「最新日期」的異動作業紀錄，過去的紀錄不追究。
            當人員於AUO無異動紀錄，回傳false，不比對詳細資料。
            當人員於Femas無異動紀錄，直接回傳AUO異動紀錄。
            當人員於AUO、Femas都有異動紀錄，比對異動類型是否一致，且AUO日期等於Femas日期，若符合條件，視為已同步過的紀錄，回傳False
        */
        if(!empty($auo_user)){
            $auo_chg = $auo_user[max(array_keys($auo_user))];
        }else{
            return false;
        }

        if(empty($auo_chg)){
            return false;
        }

        if(!empty($fs_user)){
            $fs_chg = $fs_user[max(array_keys($fs_user))];
        }else{
            return $auo_chg;
        }

        // if($auo_chg['action'] == $fs_chg['action'] && $auo_chg['date'] <= $fs_chg['date']){
        // AUO新異動作業時間必須晚於Femas最新異動紀錄的日期
        if($auo_chg['date'] <= $fs_chg['date']){
            return false;
        }else{
            return $auo_chg;
        }
    }

    public function diff_exp($fs_exp, $auo_exp){
        if(empty($auo_exp) && empty($fs_exp)){
            return false;
        }
        $tmp_fs = array();
        $tmp_auo = array();
        $upd_exp = array();

        foreach ($auo_exp as $exp) {
            $date = $exp['started_date'].' '.$exp['ended_date'];
            $tmp_auo[$date] = $exp;
        }

        foreach ($fs_exp as $exp) {
            if(empty($exp['user_change_id']) && empty($exp['department_change_id'])){
                $date = $exp['started_date'].' '.$exp['ended_date'];
                $tmp_fs[$date] = $exp;
            }
        }
        // $this->log(var_export(array($fs_exp ,$tmp_fs, $tmp_auo), true));

        $delIds = array_diff(array_keys($tmp_fs), array_keys($tmp_auo));
        $addIds = array_diff(array_keys($tmp_auo), array_keys($tmp_fs));
        $int_exps = array_intersect_assoc($tmp_auo, $tmp_fs);


        foreach ($delIds as $date) {
            $del_exp = array();
            $del_exp['id'] = $tmp_fs[$date]['id'];
            $del_exp['deleted'] = true;
            $upd_exp[] = $del_exp;
        }
        foreach ($addIds as $date) {
            $add_exp = $tmp_auo[$date];
            $upd_exp[] = $tmp_auo[$date];
        }
        foreach ($int_exps as $date => $exp) {
            $diff_exp = array();
            foreach ($exp as $key => $value) {
                if($value != $tmp_fs[$date][$key]){
                    $diff_exp[$key] = $value;
                }
            }
            if(!empty($diff_exp)){
                $diff_exp['id'] = $tmp_fs[$date]['id'];
                $upd_exp[] = $diff_exp;
            }
        }

        return $upd_exp;
    }

    public function diff_edu($fs_edu, $auo_edu){
        if(empty($auo_edu) && empty($fs_edu)){
            return false;
        }
        $tmp_fs = array();
        $tmp_auo = array();
        $upd_edu = array();

        foreach ($auo_edu as $edu) {
            $date = $edu['started_date'].' '.$edu['ended_date'];
            $tmp_auo[$date] = $edu;
        }

        foreach ($fs_edu as $edu) {
            $date = $edu['started_date'].' '.$edu['ended_date'];
            $tmp_fs[$date] = $edu;
        }

        $delIds = array_diff(array_keys($tmp_fs), array_keys($tmp_auo));
        $addIds = array_diff(array_keys($tmp_auo), array_keys($tmp_fs));
        $int_edus = array_intersect_assoc($tmp_auo, $tmp_fs);

        foreach ($delIds as $date) {
            $del_edu = array();
            $del_edu['id'] = $tmp_fs[$date]['id'];
            $del_edu['deleted'] = true;
            $upd_edu[] = $del_edu;
        }
        foreach ($addIds as $date) {
            $add_edu = $tmp_auo[$date];
            $upd_edu[] = $tmp_auo[$date];
        }
        foreach ($int_edus as $date => $edu) {
            $diff_edu = array();
            foreach ($edu as $key => $value) {
                if($value != $tmp_fs[$date][$key]){
                    $diff_edu[$key] = $value;
                }
            }
            if(!empty($diff_edu)){
                $diff_edu['id'] = $tmp_fs[$date]['id'];
                $upd_edu[] = $diff_edu;
            }
        }

        return $upd_edu;
    }

    public function diff_mil($fs_mil, $auo_mil){
        if(empty($auo_mil) && empty($fs_mil)){
            return false;
        }
        $tmp_fs = array();
        $tmp_auo = array();
        $upd_edu = array();

        foreach ($auo_mil as $mil) {
            $date = $mil['started_date'].' '.$mil['ended_date'];
            $tmp_auo[$date] = $mil;
        }

        foreach ($fs_mil as $mil) {
            $date = $mil['started_date'].' '.$mil['ended_date'];
            $tmp_fs[$date] = $mil;
        }
        $delIds = array_diff(array_keys($tmp_fs), array_keys($tmp_auo));
        $addIds = array_diff(array_keys($tmp_auo), array_keys($tmp_fs));
        $int_mils = array_intersect_assoc($tmp_auo, $tmp_fs);
        foreach ($delIds as $date) {
            $del_mil = array();
            $del_mil['id'] = $tmp_fs[$date]['id'];
            $del_mil['deleted'] = true;
            $upd_mil[] = $del_mil;
        }
        foreach ($addIds as $date) {
            $upd_mil[] = $tmp_auo[$date];
        }
        foreach ($int_mils as $date => $mil) {
            $diff_mil = array();
            foreach ($mil as $key => $value) {
                if($value != $tmp_fs[$date][$key]){
                    $diff_mil[$key] = $value;
                }
            }
            if(!empty($diff_mil)){
                $diff_mil['id'] = $tmp_fs[$date]['id'];
                $upd_mil[] = $diff_mil;
            }
        }
        return $upd_mil;
    }

    public function AuoDep(){
        $SaasSettings = TableRegistry::getTableLocator()->get('SaasSettings');
        $depMin = $SaasSettings->getSys('DepartmentMinReference');
        $depMin = (!empty($depMin) && is_numeric($depMin))? $depMin:0;
        $function = array(
            'id' => "5C59267F-A690-4437-9B59-84D5BC2013A5",
            'name' => "HR_org_data_all"//生效組織資料表
        );
        list($auo_dep, $err_msg) = $this->requestAUO($function, true);
        $record = array('sync_records_id' => $this->syncId, 'api_host' => 'AUO', 'action' => '生效組織資料表(HR_org_data_all)', 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
        $depIds = array();
        $deps = array();
        if(empty($err_msg)){
            $depIds = Hash::combine($auo_dep,'{n}.ORGID','{n}.ORGID');
            foreach ($auo_dep as $dep) {
                $start_date = explode(" ", $dep['effective_date']);
                $deps[$dep['ORGID']]['sn'] = $dep['ORGID'];
                $deps[$dep['ORGID']]['org_id'] = $dep['org_id'];
                $deps[$dep['ORGID']]['name'] = $dep['org_cname'];
                $deps[$dep['ORGID']]['brief_name'] = $dep['org_eshortname'];
                $deps[$dep['ORGID']]['start_date'] = date('Y-m-d', strtotime(array_shift($start_date)));
                $deps[$dep['ORGID']]['manager_sn'] = $dep['boss_emp_no'];
                if(!empty($dep['parent_orgid']) && in_array($dep['parent_orgid'], $depIds)){
                    $deps[$dep['ORGID']]['superior_dp_sn'] = $dep['parent_orgid'];
                }
            }
            $record['total_count'] = $this->countFuc($deps);
            if($this->countFuc($deps) <= $depMin){
                $err_msg = '生效組織資料低於閥值:'.$depMin;
                $record['msg'] = $err_msg;
                $record['status'] = 'error';
                $record['error_count'] = $this->countFuc($deps);
            }else{
                $record['success_count'] = $this->countFuc($deps);
            }
        }else{
            $record['status'] = 'error';
            $record['msg'] = $err_msg;
        }
        $this->save_sync_log($record, true);
        return array($depIds, $deps, $err_msg);
    }

    public function AuoAppover(){
        $function = array(
            'id' => "78E7E1BE-21EF-45CD-9E8A-BDEB74EBE2EC",
            'name' => "femas_approver"//簽核主管資料表
        );
        list($auo_appover, $err_msg) = $this->requestAUO($function);
        $record = array('sync_records_id' => $this->syncId, 'api_host' => 'AUO', 'action' => '簽核主管資料表(femas_approver)', 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
        $userIds = array();
        $users = array();
        if(empty($err_msg)){
            $userIds = Hash::combine($auo_appover,'{n}.emp_no','{n}.emp_no');
            foreach ($auo_appover as $user) {
                $base = array();
                $base['name'] = $user['emp_name'];
                $base['sex'] = $user['sex'];
                $base['en_name'] = $user['eng_name'];
                $base['user_grade'] = $user['emp_kind'];
                $base['job_grade'] = $user['job_idl'];
                $base['boss_sn'] = $user['boss_no'];
                $base['user_title_name'] = $user['title'];
                $base['shift_type'] = $user['class_id'];
                $base['user_type_name'] = '簽核主管'; //預設簽核主管
                $base['org_name'] = 'AUHQ';// 待確認
                $base['department_name'] = 'AUHQ';// 待確認
                $base['holiday_calendar_name'] = '越南行事曆';
                $base['cellphone'] = $user['PHS'];
                $base['officephone1'] = $user['ext_no'];
                $base['birthday'] = $this->dateFormat($user['birthday']);
                $base['arrivedate'] = $this->dateFormat($user['come_date']);
                $base['leavedate'] = $this->dateFormat($user['quit_date']);

                $users[$user['emp_no']]['base'] = $base;
            }
            $record['total_count'] = $this->countFuc($users);
            $record['success_count'] = $this->countFuc($users);
        }else{
            $record['status'] = 'error';
            $record['msg'] = $err_msg;
        }
        $this->save_sync_log($record, true);
        return array($userIds, $users, $err_msg);
    }

    public function AuoUser($deps){
        $SaasSettings = TableRegistry::getTableLocator()->get('SaasSettings');
        $userMin = $SaasSettings->getSys('UserMinReference');
        $userMin = (!empty($userMin) && is_numeric($userMin))? $userMin:0;
        $function = array(
            'id' => "BFFF3244-F851-48C7-B185-E8A41DE315B6",
            'name' => "HR_paitw01_o1"//員工基礎資料表
        );
        list($auo_user, $err_msg) = $this->requestAUO($function);
        $record = array('sync_records_id' => $this->syncId, 'api_host' => 'AUO', 'action' => '員工基礎資料表(HR_paitw01_o1)', 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
        $userIds = array();
        $users = array();
        if(empty($err_msg)){
            $userIds = Hash::combine($auo_user,'{n}.emp_no','{n}.emp_no');
            foreach ($auo_user as $user) {
                $base = array();
                $base['name'] = $user['emp_name'];
                $base['sex'] = $user['sex'];
                $base['user_title_name'] = $user['title'];
                $base['job_grade'] = $user['job_idl'];
                $base['user_type_name'] = $user['emp_type'];
                $base['card_number'] = $user['emp_card'];
                $base['department_name'] =  $deps[$user['orgid']]['org_id'].' '.$deps[$user['orgid']]['name'];
                $base['boss_sn'] = $user['boss_no'];
                $base['shift_type'] = $user['class_id'];
                $base['cost_center_name'] = $user['cost_center'];
                $base['holiday_calendar_name'] = '越南行事曆';// 待確認
                $base['org_name'] = 'AUVN';// 待確認
                $base['birthday'] = $this->dateFormat($user['birthday']);
                $base['arrivedate'] = $this->dateFormat($user['come_date']);
                $base['leavedate'] = $this->dateFormat($user['quit_date']);

                $advanced_fields = array(
                    'q7' => (isset($user['boss_level'])? $user['boss_level']:''),// 待確認
                    'q9' => (isset($user['job_key'])? $user['job_key']:'')// 待確認
                );

                $base['advanced_fields'] = $advanced_fields;
                $users[$user['emp_no']]['base'] = $base;
            }
            $record['total_count'] = $this->countFuc($users);
            if($this->countFuc($users) <= $userMin){
                $err_msg = '員工基礎資料低於閥值：'.$userMin;
                $record['msg'] = $err_msg;
                $record['status'] = 'error';
                $record['error_count'] = $this->countFuc($users);
            }else{
                $record['success_count'] = $this->countFuc($users);
            }
        }else{
            $record['status'] = 'error';
            $record['msg'] = $err_msg;
        }
        $this->save_sync_log($record, true);
        return array($userIds, $users, $err_msg);
    }

    public function AuoUser2(){
        $SaasSettings = TableRegistry::getTableLocator()->get('SaasSettings');
        $user2Min = $SaasSettings->getSys('User2MinReference');
        $user2Min = (!empty($user2Min) && is_numeric($user2Min))? $user2Min:0;
        $function = array(
            'id' => "59FB78CD-E156-458F-AF20-BA509FA749A9",
            'name' => "HR_paitw05_o7"//員工進階資料表
        );
        list($auo_user, $err_msg) = $this->requestAUO($function);
        $record = array('sync_records_id' => $this->syncId, 'api_host' => 'AUO', 'action' => '員工進階資料表(HR_paitw05_o7)', 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
        $userIds = array();
        $users = array();
        if(empty($err_msg)){
            $userIds = Hash::combine($auo_user,'{n}.EMP_NO','{n}.EMP_NO');
            $fs_country = $this->FemasCountry();
            foreach ($auo_user as $user) {
                $base = array();
                $country = strtoupper($user['NATION']);
                $base['country_sn'] = (!empty($fs_country[$country]))? $fs_country[$country]:'';
                $base['married'] = ($user['MARRIED'] == 'Marr.')? '1':'0';
                $base['passport_number'] = $user['PASSPORT_NO'];
                $base['passport_name'] = $user['PASSPORT_NAME'];
                $base['user_grade'] = $user['GRADE'];
                $base['back_seniority'] = (!empty($user['BEFORE_YEAR']))? ceil(floatval($user['BEFORE_YEAR'])):0;
                $addr = array_filter([
                    isset($user['PER_ADD1']) ? $user['PER_ADD1'] : '',
                    isset($user['PER_ADD2']) ? $user['PER_ADD2'] : '',
                    isset($user['PER_ADD3']) ? $user['PER_ADD3'] : '',
                    isset($user['PER_ADD4']) ? $user['PER_ADD4'] : ''
                ]);
                $base['addr'] = implode('', $addr);
                $cont_addr = array_filter([
                    isset($user['NOWADD1']) ? $user['PER_ADD1'] : '',
                    isset($user['NOWADD2']) ? $user['PER_ADD2'] : '',
                    isset($user['NOWADD3']) ? $user['PER_ADD3'] : '',
                    isset($user['NOWADD4']) ? $user['PER_ADD4'] : ''
                ]);
                $base['cont_addr'] = implode('', $cont_addr);
                $base['homephone'] = $user['PER_TEL'];
                $base['cellphone'] = $user['MOBILE1'];
                $base['officephone1'] = $user['NOW_TEL1'];
                $base['officephone2'] = $user['NOW_TEL2'];
                $base['emergency_contact1'] = $user['EMEG_PER1'];
                $base['emergency_phone1'] = $user['EMEG_TEL1'];
                $base['emergency_contact2'] = $user['EMEG_PER2'];
                $base['emergency_phone2'] = $user['EMEG_TEL2'];

                $base['advanced_fields'] = array(
                    'q8' => (isset($user['LOCAL_GRADE'])? $user['LOCAL_GRADE']:'')// 待確認
                );
                //兵役
                $base['military_service_record'] = array();
                $military_service_record = array();
                if(!empty($user['MILITARY_BDATE'])){
                    $military_service_record['started_date'] = substr($user['MILITARY_BDATE'], 0, 4).'-'.substr($user['MILITARY_BDATE'], 4, 2).'-'.substr($user['MILITARY_BDATE'], 6, 2);
                }
                if(!empty($user['MILITARY_EDATE'])){
                    $military_service_record['ended_date'] = substr($user['MILITARY_EDATE'], 0, 4).'-'.substr($user['MILITARY_EDATE'], 4, 2).'-'.substr($user['MILITARY_EDATE'], 6, 2);
                }

                if(!empty($military_service_record)){
                    $military_service_record['army'] = '陸軍';
                    $base['military_service_record'][] = $military_service_record;

                }
                $users[$user['EMP_NO']]['base'] = $base;
                if(!empty($user['BANK_KEY']) && !empty($user['BANK_NUMBER'])){
                    $salary['tmp_Payment1'] = '轉帳/'.$user['BANK_KEY'].'/'.$user['BANK_NUMBER']; //待確認
                }
                $users[$user['EMP_NO']]['salary'] = $salary;
            }
            $record['total_count'] = $this->countFuc($users);
            if($this->countFuc($users) <= $user2Min){
                $err_msg = '員工進階資料低於閥值：'.$user2Min;
                $record['msg'] = $err_msg;
                $record['status'] = 'error';
                $record['error_count'] = $this->countFuc($users);
            }else{
                $record['success_count'] = $this->countFuc($users);
            }
        }else{
            $record['status'] = 'error';
            $record['msg'] = $err_msg;
        }
        $this->save_sync_log($record, true);
        return array($userIds, $users, $err_msg);
    }

    public function AuoUserIntegrate($params, $appover = false){
        extract($params);
        $users = array();
        if(empty($appover)){
            foreach ($uids as $id) {
                $users[$id]['base'] = array();
                if(!empty($auo_user1s) || !empty($auo_user2s)){
                    $user = $auo_user1s[$id];
                    if(!empty($auo_user2s[$id])){
                        if(empty($user)){
                            $users[$id]['base'] = $auo_user2s[$id]['base'];
                        }else{
                            $advanced_fields = array_merge($user['base']['advanced_fields'], $auo_user2s[$id]['base']['advanced_fields']);
                            $users[$id]['base'] = array_merge($user['base'], $auo_user2s[$id]['base']);
                            $users[$id]['base']['advanced_fields'] = $advanced_fields;
                        }
                    }else{
                        $users[$id]['base'] = $user['base'];
                    }

                    if(!empty($auo_user1s) && !in_array($users[$id]['base']['boss_sn'], $auoAllUids)){
                        unset($users[$id]['base']['boss_sn']);
                    }
                }


                if(!empty($auo_edu[$id])){
                    $users[$id]['base']['education_record'] = $auo_edu[$id];
                }else{
                    //$users[$id]['base']['education_record'] = array();
                }

                if(!empty($auo_exp[$id])){
                    $users[$id]['base']['work_experience'] = $auo_exp[$id];
                }else{
                    //$users[$id]['base']['work_experience'] = array();
                }
            }
        }else{
            foreach ($auo_user1s as $id => $user) {
                $users[$id]['base'] = $user['base'];
                if(!in_array($users[$id]['base']['boss_sn'], $auoAllUids)){
                    unset($users[$id]['base']['boss_sn']);
                }
            }
        }

        return $users;
    }

    public function AuoExp(){
        $function = array(
            'id' => "19BEB8EE-7D80-4A5B-BB72-A7B461AE41E7",
            'name' => "HR_paitw05_o2"//員工經歷資料表
        );
        list($auo_exp, $err_msg) = $this->requestAUO($function);
        $record = array('sync_records_id' => $this->syncId, 'api_host' => 'AUO', 'action' => '員工經歷資料表(HR_paitw05_o2)', 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
        $exps = array();
        $userIds = array();
        if(empty($err_msg)){
            $userIds = Hash::combine($auo_exp,'{n}.EMP_NO','{n}.EMP_NO');
            foreach ($auo_exp as $exp) {
                $tmp_exp = array();
                $tmp_exp['org_name'] = $exp['COMPANY'];
                $tmp_exp['user_title_name'] = $exp['TITLE'];
                $tmp_exp['seniority'] = ceil((float)$exp['PRO_YEAR']*12);
                if(!empty($exp['BEGIN_DATE'])){
                    $tmp_exp['started_date'] = substr($exp['BEGIN_DATE'], 0, 4).'-'.substr($exp['BEGIN_DATE'], 4, 2).'-'.substr($exp['BEGIN_DATE'], 6, 2);
                }

                if(!empty($exp['END_DATE'])){
                    $tmp_exp['ended_date'] = substr($exp['END_DATE'], 0, 4).'-'.substr($exp['END_DATE'], 4, 2).'-'.substr($exp['END_DATE'], 6, 2);
                }

                $exps[$exp['EMP_NO']][] = $tmp_exp;
            }
            $record['total_count'] = $this->countFuc($exps);
            $record['success_count'] = $this->countFuc($exps);
        }else{
            $record['status'] = 'error';
            $record['msg'] = $err_msg;
        }
        $this->save_sync_log($record);
        return array($userIds, $exps);
    }

    public function AuoEdu(){
        $function = array(
            'id' => "0E3D6A2D-F1EC-4B45-9EC0-11C0C7733EBB",
            'name' => "HR_paitw05_o1"//員工學歷資料表
        );
        list($auo_edu, $err_msg) = $this->requestAUO($function);
        $record = array('sync_records_id' => $this->syncId, 'api_host' => 'AUO', 'action' => '員工學歷資料表(HR_paitw05_o1)', 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
        $edus = array();
        $userIds = array();
        if(empty($err_msg)){
            $fs_education_type = $this->FemasEducationType();
            $userIds = Hash::combine($auo_edu,'{n}.EMP_NO','{n}.EMP_NO');
            foreach ($auo_edu as $edu) {
                $tmp_edu = array();
                $tmp_edu['name'] = $edu['SCHOOL_NAME'];
                $tmp_edu['department_name'] = $edu['COURSE_NAME'];
                $tmp_edu['type_name'] = $fs_education_type[$edu['DEGREE']];; //待確認
                if(!empty($edu['BEGIN_DATE'])){
                    $tmp_edu['started_date'] = substr($edu['BEGIN_DATE'], 0, 4).'-'.substr($edu['BEGIN_DATE'], 4, 2).'-'.substr($edu['BEGIN_DATE'], 6, 2);
                }

                if(!empty($edu['END_DATE'])){
                    $tmp_edu['ended_date'] = substr($edu['END_DATE'], 0, 4).'-'.substr($edu['END_DATE'], 4, 2).'-'.substr($edu['END_DATE'], 6, 2);
                }

                $tmp_edu['status'] = (!empty($edu['GRADUATE']))? 'graduated':'dropped';
                $edus[$edu['EMP_NO']][] = $tmp_edu;
            }
            $record['total_count'] = $this->countFuc($edus);
            $record['success_count'] = $this->countFuc($edus);
        }else{
            $record['status'] = 'error';
            $record['msg'] = $err_msg;
        }
        $this->save_sync_log($record);
        return array($userIds, $edus);
    }

    public function AuoUserChange($uids = array()){
        $function = array(
            'id' => "4B7180CB-8D56-4FDA-A315-90D0FF065F76",
            'name' => "HR_paitw05_act"//員工經歷資料表
        );
        list($auo_chg, $err_msg) = $this->requestAUO($function);
        $record = array('sync_records_id' => $this->syncId, 'api_host' => 'AUO', 'action' => '員工經歷資料表(HR_paitw05_act)', 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
        if(empty($err_msg)){
            $SaasSettings = TableRegistry::getTableLocator()->get('SaasSettings');
            $NewUser = explode(',', $SaasSettings->getSys('NewUser'));
            $UserChange = explode(',', $SaasSettings->getSys('UserChange'));
            $LeaveUser = explode(',', $SaasSettings->getSys('LeaveUser'));
            $UnpaidLeaveUser = explode(',', $SaasSettings->getSys('UnpaidLeaveUser'));
            $Reinstate = explode(',', $SaasSettings->getSys('Reinstate'));

            $keys = array_merge($NewUser, $UserChange, $LeaveUser, $UnpaidLeaveUser, $Reinstate);
            $values = array_merge(
                array_fill(0, $this->countFuc($NewUser), 'NewUser'),
                array_fill(0, $this->countFuc($UserChange), 'UserChange'),
                array_fill(0, $this->countFuc($LeaveUser), 'LeaveUser'),
                array_fill(0, $this->countFuc($UnpaidLeaveUser), 'UnpaidLeaveUser'),
                array_fill(0, $this->countFuc($Reinstate), 'Reinstate')
            );
            $action = array_combine($keys, $values);
            $chgs = array();
            foreach ($auo_chg as $chg) {
                if(!empty($chg['emp_no']) && in_array($chg['emp_no'], $uids)){
                    if(!empty($action[$chg['action_type']]) && $action[$chg['action_type']] != 'NewUser'){
                        $date = substr($chg['trans_date'], 0, 4).'-'.substr($chg['trans_date'], 4, 2).'-'.substr($chg['trans_date'], 6, 2);

                        $tmp_chg = array();
                        $tmp_chg['action'] = $action[$chg['action_type']];
                        $tmp_chg['date'] = $date;
                        $tmp_chg['reason'] = $chg['reason'];
                        $tmp_chg['employ_sta_name'] = $chg['employ_sta_name'];

                        if($chg['employ_sta_name'] == 'Withdrawn'){
                            $tmp_chg['action'] = 'LeaveUser';
                        }elseif($chg['employ_sta_name'] == 'Inactive'){
                            $tmp_chg['action'] = 'UnpaidLeaveUser';
                        }
                        $chgs[$chg['emp_no']][$date] = $tmp_chg;
                    }
                }
            }
            $record['total_count'] = count($chgs);
            $record['success_count'] = count($chgs);
        }else{
            $record['status'] = 'error';
            $record['msg'] = $err_msg;
        }

        return $chgs;
    }
    public function FemasUser($org = 'AUVN', $auo_deps = array()){
        $action = 'su_users.json';
        $request =  ["orgName" => $org]; //排除簽核主管AUHQ
        list($res, $err_msg)  = $this->requestFemas($action, $request);
        $femas_user = $res['datas'];
        $record = array('sync_records_id' => $this->syncId, 'action' => $org.'人事資料(su_users)', 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
        $users = array();
        $userIds = array();
        if(empty($err_msg)){
            foreach ($femas_user as $user) {
                if($user['sn'] != 'UPT002'){
                    $users[$user['sn']] = $user;
                    if($org == 'AUVN'){
                        $users[$user['sn']]['department_name'] = $auo_deps[$user['department_sn']]['org_id'].' '.$auo_deps[$user['department_sn']]['name'];
                    }else{
                        $users[$user['sn']]['department_name'] = 'AUHQ';
                    }

                    if($users[$user['sn']]['leavedate'] == '0000-00-00'){
                        $users[$user['sn']]['leavedate'] = '';
                    }
                    $users[$user['sn']]['user_title_name'] = $user['user_title'];
                    $users[$user['sn']]['user_type_name'] = $user['user_type'];
                    $users[$user['sn']]['advanced_fields'] = Hash::combine($user['advanced_fields'], '{n}.code', '{n}.value');
                    unset($users[$user['sn']]['user_title']);
                }
            }
            $userIds = Hash::combine($femas_user,'{n}.sn','{n}.sn');
            unset($userIds['UPT002']);
            $record['total_count'] = $this->countFuc($femas_user);
            $record['success_count'] = $this->countFuc($femas_user);
        }else{
            $record['status'] = 'error';
            $record['msg'] = $err_msg;
        }
        $this->save_sync_log($record, true);
        return array($userIds, $users, $err_msg);
    }

    public function FemasDep(){
        $action = 'su_departments.json';
        $request = ["findFields" => ["sn","name","start_date","director_sn","superior_dp_sn", "brief_name"]];
        $record = array('sync_records_id' => $this->syncId, 'action' => '部門資料(su_departments)', 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
        list($res, $err_msg)  = $this->requestFemas($action, $request);
        $femas_dep = $res['datas'];
        $deps = array();
        $depIds = array();
        if(empty($err_msg)){
            $deps = Hash::combine($femas_dep,'{n}.sn','{n}');
            $depIds = Hash::combine($femas_dep,'{n}.sn','{n}.sn');
            foreach ($deps as $id => $dep) {
                $name_string = explode(" ", $dep['name']);
                $deps[$id]['org_id'] = array_shift($name_string);
                $deps[$id]['name'] = implode(" ", $name_string);
                $deps[$id]['manager_sn'] = $dep['director_sn'];
            }
            $record['total_count'] = $this->countFuc($deps);
            $record['success_count'] = $this->countFuc($deps);
        }else{
            $record['status'] = 'error';
            $record['msg'] = $err_msg;
        }
        $this->save_sync_log($record, true);
        return array($depIds, $deps, $err_msg);
    }

    public function FemasCountry(){
        $action = 'su_countrys.json';
        $request = [];
        list($res, $err_msg)  = $this->requestFemas($action, $request);
        $femas_country = $res['datas'];
        $record = array('sync_records_id' => $this->syncId, 'action' => '國別資料(su_countrys)', 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
        $country = array();
        if(empty($err_msg)){
            $country = Hash::combine($femas_country, '{n}.en_name', '{n}.sn');
            $record['total_count'] = $this->countFuc($country);
            $record['success_count'] = $this->countFuc($country);
        }else{
            $record['status'] = 'error';
            $record['msg'] = $err_msg;
        }
        $this->save_sync_log($record);
        return $country;
    }

    public function FemasEducationType(){
        $action = 'su_education_types.json';
        $request = [];
        list($res, $err_msg)  = $this->requestFemas($action, $request);
        $femas_edu_type = $res['datas'];
        $record = array('sync_records_id' => $this->syncId, 'action' => '學歷類別資料(su_education_types)', 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
        $edu_type = array();
        if(empty($err_msg)){
            $edu_type = Hash::combine($femas_edu_type, '{n}.code', '{n}.name');
            $record['total_count'] = $this->countFuc($edu_type);
            $record['success_count'] = $this->countFuc($edu_type);
        }else{
            $record['status'] = 'error';
            $record['msg'] = $err_msg;
        }
        $this->save_sync_log($record);
        return $edu_type;
    }

    public function FemasUserChange($uids = array()){
        $action = 'su_user_changes.json';
        $postFields = ["userSn" => implode(',', $uids)];
        list($res, $err_msg) = $this->requestFemas($action, $postFields);
        $fs_chg = $res['datas'];
        $record = array('sync_records_id' => $this->syncId, 'action' => '人事異動資料(su_user_changes)', 'status' => 'success' ,'created' => date('Y-m-d H:i:s'));
        $chgs = array();
        if(empty($err_msg)){
            $fs_statusOps = array(
                '1' => 'NewUser',
                '30' => 'UserChange',
                '51' => 'UserChange',
                '23' => 'UserChange',
                '24' => 'UserChange',
                '2' => 'UserChange',
                '52' => 'UserChange',
                '26' => 'UserChange',
                '20' => 'UserChange',
                '27' => 'UserChange',
                '25' => 'UserChange',
                '22' => 'UserChange',
                '91' => 'LeaveUser',
                '92' => 'LeaveUser',
                '93' => 'LeaveUser',
                '94' => 'LeaveUser',
                '95' => 'LeaveUser',
                '21' => 'UnpaidLeaveUser',
                '3' => 'Reinstate'
            );
            foreach ($fs_chg as $chg) {
                if(!empty($fs_statusOps[$chg['change_type_code']])){
                    $date = $chg['start_date'];
                    $tmp_chg = array();
                    $tmp_chg['action'] = $fs_statusOps[$chg['change_type_code']];
                    $tmp_chg['date'] = $date;
                    $chgs[$chg['user_sn']][$date] = $tmp_chg;
                }
            }
            $record['total_count'] = $this->countFuc($chgs);
            $record['success_count'] = $this->countFuc($chgs);
        }else{
            $record['status'] = 'error';
            $record['msg'] = $err_msg;
        }
        $this->save_sync_log($record);
        return $chgs;
    }

    public function requestAUO($function, $dep=false){
        $err_msg = false;
        if($this->test){
        	$filePath = WWW_ROOT . 'js' . DS . 'api' . DS;
            $response = file_get_contents($filePath.$function['name'].'.json');
            $res = json_decode($response, true);
        }else{
        	$SaasSettings = TableRegistry::getTableLocator()->get('SaasSettings');
            $host = $SaasSettings->getSys('AUOHost');
            $guid = $SaasSettings->getSys('AUOguid');
            $CompanyId = $SaasSettings->getSys('AUOCompanyId');
            $cond = !empty($dep)? 'org_id':'empno';
            $http = new Client(['timeout' => $this->timeout]);
            $headers = [
                'Token' => $this->auo_token
            ];
            $request = [
                'SysId' => '10157',
                'CompanyId' => $CompanyId,
                'ApiFuncId' => $function['id'],
                'AuthFunctionName' => $function['name'],
                'Params' => '{"guid":"'.$guid.'","'.$cond.'":"All"}'
            ];

            try {
                $response = $http->post($host.'CallAPI', $request, ['headers' => $headers]);
                if ($response->isOk()) {
                    $res = $response->getJson();
                    if($res['Result'] == null){
                        $err_msg = $res['Message'];
                    }
                }else{
                    $err_msg = 'statusCode：'. $response->getStatusCode();
                }
            } catch (Exception $e) {
                $res['Result'] = false;
                $err_msg = "發生錯誤：" . $e->getMessage();
            }
        }
        return array($res['Result'], $err_msg);
    }

    public function getAUOToken(){
        $SaasSettings = TableRegistry::getTableLocator()->get('SaasSettings');
        $host = $SaasSettings->getSys('AUOHost');
        $ip = $SaasSettings->getSys('AUOip');
        $pwd = $SaasSettings->getSys('AUOpwd');
        $http = new Client(['timeout' => $this->timeout]);
        $headers = [
            'ip' => $ip,
            'pwd' => $pwd,
            'Content-Type' => 'application/json'
        ];
        $token = array();
        try {
            $response = $http->get($host.'GetApiToken', [], ['headers' => $headers]);
            if ($response->isOk()) {
                $res = $response->getJson();
                if($res['Code'] == '1'){
                    $token['token'] = $res['Result']['Token'];
                    $token['status'] = 'ok';
                    $this->auo_token = $token['token'];
                }else{
                    $token['Message'] = $res['Message'];
                    $token['status'] = 'err';
                }
            } else {
                $token['Message'] = 'statusCode：'. $response->getStatusCode();
                $token['status'] = 'err';
            }
        } catch (Exception $e) {
            $token['Message'] = "發生錯誤：" . $e->getMessage();
            $token['status'] = 'err';
        }

        return $token;
    }

    public function requestFemas($action, $request){
        $SaasSettings = TableRegistry::getTableLocator()->get('SaasSettings');
        $host = $SaasSettings->getSys('FemasHost');
        $token = $SaasSettings->getSys('FemasToken');
        $url = $host.$action;
        $http = new Client(['timeout' => $this->timeout]);
        $headers = [
            'Authorization' => $token,
            'Content-Type' => 'application/json'
        ];
        $err_msg = '';
        try {
            $response = $http->post($url, json_encode($request, JSON_UNESCAPED_UNICODE), ['headers' => $headers]);
            if ($response->isOk()) {
                $res = $response->getJson();
                if(isset($res['err_msg']) && $res['status'] != 'success'){
                    $err_msg = $res['err_msg'];
                }
            }else{
                $err_msg = 'statusCode：'. $response->getStatusCode();
            }
        } catch (Exception $e) {
            $res['response'] = false;
            $err_msg = "發生錯誤：" . $e->getMessage();
        }

        return array($res['response'], $err_msg);
    }

    public function save_sync_record($data){
    	$SyncRecords = TableRegistry::getTableLocator()->get('SyncRecords');
        if(!empty($data['id'])){
            $syncRecord = $SyncRecords->get($data['id'], [
                'contain' => [],
            ]);
        }else{
            $syncRecord = $SyncRecords->newEmptyEntity();
        }
        $syncRecord = $SyncRecords->patchEntity($syncRecord, $data);

        if($SyncRecords->save($syncRecord)){
            if(empty($data['id'])){
                $this->syncId = $syncRecord->get('id');
            }
        }
    }

    public function save_sync_log($data, $sendMail = false){
    	$SyncLogs = TableRegistry::getTableLocator()->get('SyncLogs');
        $syncLog = $SyncLogs->newEmptyEntity();
        $syncLog = $SyncLogs->patchEntity($syncLog, $data);
        $SyncLogs->save($syncLog);

        if(!empty($sendMail) && !empty($data['msg']) && $data['status'] == 'error'){
            $msg = $data['msg'];
            $this->sendMail($data, false);
        }
    }

    public function countFuc($data){
        if(!empty($data)){
            return count($data);
        }else{
            return 0;
        }
    }

	public function dateFormat($date) {
	    $format_date = null;
	    if (!empty($date)) {
	        try {
	            $new_date = explode(" ", $date);

	            // 支援多種日期格式，避免格式錯誤
	            $formats = ['Y/n/j', 'Y-m-d'];
	            $datetime = false;

	            // 嘗試不同格式直到成功解析
	            foreach ($formats as $format) {
	                $datetime = \DateTime::createFromFormat($format, $new_date[0]);
	                if ($datetime !== false) {
	                    break;
	                }
	            }

	            // 如果解析成功，格式化為 'Y-m-d'
	            if ($datetime) {
	                $format_date = $datetime->format('Y-m-d');
	            }
	        } catch (Exception $e) {
	            // 可以記錄錯誤或處理例外狀況
	        }
	    }
	    return $format_date;
	}

}

?>