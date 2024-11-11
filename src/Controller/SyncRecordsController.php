<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\EventInterface;
use Cake\Http\Client;
use Cake\Utility\Hash;
use Exception;
use App\Service\SyncService;


/**
 * SyncRecords Controller
 *
 * @property \App\Model\Table\SyncRecordsTable $SyncRecords
 * @method \App\Model\Entity\SyncRecord[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SyncRecordsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public $auo_token;
    public $advancePermission;
    public $test = true;
    public $timeout = 3600;

    public function beforeFilter(EventInterface $event): void{
        parent::beforeFilter($event);
        $this->_advancePermission('SyncRecords');

    }

    public function index() {
        $this->viewBuilder()->setLayout('sidebar');
    }

    public function listing() {
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false; // 禁用自動渲染
        $this->do_list();
    }

    public function do_list($paginate = true){
        $w2Params = json_decode($this->request->getQuery('request'), true);
        $this->loadModel('SaasAdmins');
        $this->SaasAdmins->showAll = true;
        $this->SyncRecords->hasOne('SaasAdmins', [
            'foreignKey' => false,
            'joinType' => 'LEFT', // 可根據需求調整
            'conditions' => ['SyncRecords.saas_admin_id = SaasAdmins.id'],
            'fields' => ['SaasAdmins.id', 'SaasAdmins.username', 'SaasAdmins.name']
        ]);
        $findOpt = [
            'limit' => $w2Params['limit'],
            'contain' => ['SaasAdmins']
        ];
        if(!empty($w2Params['sort'])){
            switch ($w2Params['sort'][0]['field']) {
                case 'name':
                    $w2Params['sort'][0]['field'] = 'SaasAdmin.name';
                    break;
                case 'username':
                    $w2Params['sort'][0]['field'] = 'SaasAdmin.username';
                    break;
                case 'visit_time':
                    $w2Params['sort'][0]['field'] = 'SaasLoginRecord.created';
                    break;
                case 'last_visit_from':
                    $w2Params['sort'][0]['field'] = 'SaasLoginRecord.ip';
                    break;
                case 'success':
                    $w2Params['sort'][0]['field'] = 'SaasLoginRecord.success';
                    break;
            }
            $order = ['order' => [$w2Params['sort'][0]['field'] => $w2Params['sort'][0]['direction']]];
        }else{
            $order = ['order' => ['SyncRecords.id' => 'desc']];
        }

        $findOpt = array_merge($findOpt, $order);
        if (!empty($paginate)){
            $statusText = array('success'=>'成功', 'warning'=>'部分錯誤' ,'error'=>'錯誤', 'waiting'=> '處理中');
            $statusColor = array('success'=>'green', 'warning'=>'#ff902e' ,'error'=>'#c71414');
            $nowPage = ($w2Params['offset']/$w2Params['limit']) + 1;

            $query = $this->SyncRecords->find()
                ->select($this->SyncRecords)
                ->select([
                    'SaasAdmins.username', 
                    'SaasAdmins.name'
                ])
                ->contain(['SaasAdmins']);
            $sync_records = $this->paginate($query, $findOpt);
            
            $records = array();
            foreach ($sync_records as $k => $sync_record) {
                $status = '';
                $bgcolor = '';
                $records[$k]['recid'] = $sync_record->id;
                $records[$k]['type'] = (!empty($sync_record->saas_admin->name) ? '手動執行':'自動排程');
                $records[$k]['user_total'] = $sync_record->user_total;
                $records[$k]['user_update'] = $sync_record->user_update;
                $records[$k]['department_total'] = $sync_record->department_total;
                $records[$k]['department_update'] = $sync_record->department_update;
                $records[$k]['username'] = (!empty($sync_record->saas_admin_id))? $sync_record->saas_admin->name:'';
                $records[$k]['ip_address_ip'] = (!empty($sync_record->ip_address_ip))? $sync_record->ip_address_ip:'';
                $records[$k]['status'] = '<span style="font-weight:bold;color:'.$statusColor[$sync_record->status].'">'.$statusText[$sync_record->status].'</span>';
                $records[$k]['created'] = $sync_record->created->format('Y-m-d H:i:s');
            }
            $results = array('total' => $this->request->getAttribute('paging')['SyncRecords']['count'], 'offset' => $w2Params['offset'], 'records' => $records);
            $this->viewBuilder()->setLayout('txt');
            $this->set('jsonData', $results);
            $this->render('/element/in_json');
        }
    }

    public function logListing($record_id){
        $this->viewBuilder()->setLayout('simple');
        $this->loadModel('SyncRecords');
        $fields = array('user_total' ,'user_update' ,'department_total' ,'department_update', 'created');
        $sync_records = $this->SyncRecords->find()
        ->where(['`id`' => $record_id])
        ->select($fields)
        ->first();
        $this->set('count', $sync_records);
        $this->set('created', $sync_records->created->format('Y-m-d H:i:s'));
        $this->set('record_id', $record_id);
    }

    public function synclog($record_id) {
        $this->viewBuilder()->setLayout('ajax');
        $this->do_loglist($record_id);
    }

    public function do_loglist($record_id){
        $this->viewBuilder()->setLayout('simple');
        $w2Params = json_decode($this->request->getQuery('request'), true);
        $this->loadModel('SyncLogs');
        if(!empty($w2Params['sort'])){
            switch ($w2Params['sort'][0]['field']) {
                case 'type':
                    $w2Params['sort'][0]['field'] = 'type';
                    break;
                case 'api_host':
                    $w2Params['sort'][0]['field'] = 'api_host';
                    break;
                case 'action':
                    $w2Params['sort'][0]['field'] = 'action';
                    break;
                case 'total':
                    $w2Params['sort'][0]['field'] = 'total';
                    break;
                case 'success':
                    $w2Params['sort'][0]['field'] = 'success';
                    break;
                case 'error':
                    $w2Params['sort'][0]['field'] = 'error';
                    break;
                case 'status':
                    $w2Params['sort'][0]['field'] = 'status';
                    break;
                case 'created':
                    $w2Params['sort'][0]['field'] = 'created';
                    break;
            }
            $order = [$w2Params['sort'][0]['field'] => $w2Params['sort'][0]['direction']];
        }else{
            $order = ['id' => 'desc'];
        }

        $findOpt = [
            'limit' => $w2Params['limit'],
            'order' => $order
        ];

        $query = $this->SyncLogs->find()
            ->select($this->SyncLogs)
            ->where([
                'sync_records_id'=>$record_id
            ]);
        $sync_logs = $this->paginate($query, $findOpt);
        $records = array();
        $statusText = array('success'=>'成功', 'warning'=>'部分錯誤' ,'error'=>'錯誤', 'waiting'=> '處理中');
        $statusColor = array('success'=>'green', 'warning'=>'#ff902e' ,'error'=>'#c71414');
        $typeText = array('scan' => '資料撈取', 'sync' => '資料同步');
        foreach ($sync_logs as $k => $sync_log) {
            $records[$k]['recid'] = $sync_log->id;
            $records[$k]['action'] = $sync_log->action;
            $records[$k]['type'] = $typeText[$sync_log->type];
            $records[$k]['api_host'] = $sync_log->api_host;
            $records[$k]['total'] = $sync_log->total_count;
            $records[$k]['success'] = $sync_log->success_count;
            $records[$k]['error'] = $sync_log->error_count;
            $records[$k]['status'] = '<span style="font-weight:bold;color:'.$statusColor[$sync_log->status].'">'.$statusText[$sync_log->status].'</span>';
            $records[$k]['created'] = $sync_log->created->format('Y-m-d H:i:s');
        }
        $results = array('total' => -1, 'offset' => $w2Params['offset'], 'records' => $records);
        $this->viewBuilder()->setLayout('txt');
        $this->set('jsonData', $results);
        $this->render('/element/in_json');
    }

    public function showMsg($logId){
        $this->viewBuilder()->setLayout('simple');
        $this->loadModel('SyncLogs');
        if($logId) {
            $msg = $this->SyncLogs->find()
            ->select('msg')
            ->where(['id' => $logId])
            ->first();
        }
        $this->set('msg', $msg['msg']);
    }

    public function syncSetting(){
        $this->viewBuilder()->setLayout('simple');
    }

    public function doSync(){
        $data = $this->request->getData();
        $syncService = new SyncService();
        $result = $syncService->doSync($data, $this->emUid);
        $this->set('jsonData', array('status' => $result));
        $this->viewBuilder()->setLayout('txt');
        $this->render('/element/in_json');
    }


}
