<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\EventInterface;
use Cake\Http\Client;
/**
 * SaasSettings Controller
 *
 * @property \App\Model\Table\SaasSettingsTable $SaasSettings
 * @method \App\Model\Entity\SaasSetting[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SaasSettingsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */

    var $isArrayKeys = array('auto_mail', 'auto_mail2', 'trial_mail', 'regain_mail', 'placeOrder_mail', 'completeOrder_mail', 'cancelOrder_mail', 'Maintainers', 'pay_setting', 'rentend_reason');


    public $advancePermission;

    public function beforeFilter(EventInterface $event): void{
        parent::beforeFilter($event);
        $this->_advancePermission('SaasSettings');
    }

    public function saasSettingsIndex(){
        $this->viewBuilder()->setLayout('sidebar');
        $show_expire_extension = $this->SaasSettings->find()->where(['`key`' => 'show_expire_extension'])->first();
        $showExpireExtension = $show_expire_extension->value;
        $fonsen_email = $this->SaasSettings->find()->where(['`key`' => 'fonsen_email'])->first();
        $fonsenMailSettings = explode(',', $fonsen_email->value);
        foreach($fonsenMailSettings as $tmail){
            $fonsenMails[$tmail] = $tmail;
        }        
        $SaasSetting = $this->SaasSettings->find('list', [
            'keyField' => 'key',
            'valueField' => 'value'
        ])
        ->where(['`type`' => 'S'])
        ->toArray();
        $this->set(compact('SaasSetting', 'fonsenMails', 'showExpireExtension'));
    }

    public function settingForm(){
        $this->viewBuilder()->setLayout('ajax');

        if($this->request->is('post')) {
            foreach ($this->request->getData('SaasSetting') as $key => $value) {
                $data['key'] = $key;
                $data['value'] = $value;
                $setting = $this->SaasSettings->find('all', [
                    'conditions' => ['SaasSettings.key' => $key]
                ])->first();
                if(!$setting){
                    $setting = $this->SaasSettings->newEmptyEntity();
                }
                $setting = $this->SaasSettings->patchEntity($setting, [
                    '`key`' => $key,
                    'value' => $value
                ]);
                $this->SaasSettings->save($setting);
            }
        }
        $this->viewBuilder()->setLayout('txt');
        $this->set('jsonData', 'ok');
        $this->render('/element/in_json');
        $this->set(compact('autoMailOpts', 'templeteOpts', 'fonsenMails', 'autoMailStatusOpts'));
    }

    public function femasTest(){
        $this->viewBuilder()->setLayout('ajax');
        if($this->request->is('post')) {
            $host = $this->request->getData('SaasSetting.FemasHost');
            $token = $this->request->getData('SaasSetting.FemasToken');
            $action = $this->request->getData('fs_test_action').'.json';
            $http = new Client();
            $url = $host.$action;
            $headers = [
                'Authorization' => $token,  // 添加你的 token
                'Content-Type' => 'application/json'        // 設定內容類型
            ];
            $response = $http->post($url, json_encode([]), ['headers' => $headers]);
            $res = array();
            $this->log(var_export($response, true));
            if ($response->isOk()) {
                $res['data'] = $response->getJson();
                $res['status'] = 'ok';
            } else {
                $res['statusCode'] = $response->getStatusCode();
                $res['status'] = 'err';
            }
            $this->viewBuilder()->setLayout('txt');
            $this->set('jsonData', $res);
            $this->render('/element/in_json');
        }
    }

    public function auoTest(){
        $this->viewBuilder()->setLayout('ajax');
        if($this->request->is('post')) {
            $apiID = [
                'HR_org_data_all' => '5C59267F-A690-4437-9B59-84D5BC2013A5',
                'HR_paitw01_o1' => 'BFFF3244-F851-48C7-B185-E8A41DE315B6',
                'HR_paitw05_o7' => '59FB78CD-E156-458F-AF20-BA509FA749A9',
                'HR_paitw05_o1' => '0E3D6A2D-F1EC-4B45-9EC0-11C0C7733EBB',
                'HR_paitw05_o2' => '19BEB8EE-7D80-4A5B-BB72-A7B461AE41E7',
                'femas_approver' => '78E7E1BE-21EF-45CD-9E8A-BDEB74EBE2EC',
                'HR_paitw05_act' => '4B7180CB-8D56-4FDA-A315-90D0FF065F76'
            ];

            $getToken = $this->auoTestToken();
            if($getToken['status'] == 'ok'){
                $host = $this->request->getData('SaasSetting.AUOHost');
                $guid = $this->request->getData('SaasSetting.AUOguid');
                $CompanyId = $this->request->getData('SaasSetting.AUOCompanyId');
                $action = $this->request->getData('auo_test_action');
                $cond = ($action == 'HR_org_data_all')? 'org_id':'empno';
                $http = new Client();
                $headers = [
                    'Token' => $getToken['token']
                ];
                $request = [
                    'SysId' => '10157',
                    'CompanyId' => $CompanyId,
                    'ApiFuncId' => $apiID[$action],
                    'AuthFunctionName' => $action,
                    'Params' => '{"guid":"'.$guid.'","'.$cond.'":"All"}'
                ];
                $response = $http->post($host.'CallAPI', $request, ['headers' => $headers]);
                if ($response->isOk()) {
                    $res['data'] = $response->getJson();
                    $res['status'] = 'ok';
                } else {
                    $res['Message'] = 'statusCode：'. $response->getStatusCode();
                    $res['status'] = 'err';
                }
            }else{
                $res['status'] = 'err';
                $res['Message'] = $getToken['Message'];
            }
            $this->viewBuilder()->setLayout('txt');
            $this->set('jsonData', $res);
            $this->render('/element/in_json');
        }
    }

    public function auoTestToken(){
        $host = $this->request->getData('SaasSetting.AUOHost');
        $ip = $this->request->getData('SaasSetting.AUOip');
        $pwd = $this->request->getData('SaasSetting.AUOpwd');
        $http = new Client();
        $headers = [
            'ip' => $ip,
            'pwd' => $pwd,
            'Content-Type' => 'application/json'
        ];
        $response = $http->get($host.'GetApiToken', [], ['headers' => $headers]);
        $token = array();
        if ($response->isOk()) {
            $res = $response->getJson();
            if($res['Code'] == '1'){
                $token['token'] = $res['Result']['Token'];
                $token['status'] = 'ok';
            }else{
                $token['Message'] = $res['Message'];
                $token['status'] = 'err';
            }

        } else {
            $res['Message'] = 'statusCode：'. $response->getStatusCode();
            $token['status'] = 'err';
        }
        return $token;
    }
}
