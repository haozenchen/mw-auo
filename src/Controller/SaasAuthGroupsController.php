<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Event\EventInterface;


/**
 * SaasAuthGroups Controller
 *
 * @property \App\Model\Table\SaasAuthGroupsTable $SaasAuthGroups
 * @method \App\Model\Entity\SaasAuthGroup[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SaasAuthGroupsController extends AppController
{
    public $advancePermission;

    public function beforeFilter(EventInterface $event): void{
        parent::beforeFilter($event);
        $this->_advancePermission('SaasAuthGroups');

    }

    public function index()
    {
        $this->viewBuilder()->setLayout('sidebar');
        $this->loadModel('SaasSettings');
    }

    public function listing() {
        $this->viewBuilder()->setLayout('ajax');
        $this->do_list();
    }


    public function do_list($paginate = true){
        $w2Params = json_decode($this->request->getQuery('request'), true);
        $this->loadModel('SaasAuthGroups');
        $conditions = [];
        if($this->emUid != 1){
            $conditions = ['id not IN(1)'];
        }
        $findOpt = [
            'limit' => $w2Params['limit']
        ];
        if(!empty($w2Params['sort'])){
            switch ($w2Params['sort'][0]['field']) {
                case 'name':
                    $w2Params['sort'][0]['field'] = 'SaasAuthGroups.name';
                    break;
            }
            $order = ['order' => [$w2Params['sort'][0]['field'] => $w2Params['sort'][0]['direction']]];
        }else{
            $order = ['order' => ['SaasAuthGroups.name asc']];
        }

        if(!empty($w2Params['search'])){
            $searchCons = array();
            foreach ($w2Params['search'] as $key => $value) {
                if($value['type'] == 'text'){
                    foreach((array)$value['field'] as $sfield){
                        switch ($value['operator']) {
                            case 'is':
                                $searchCons = array_merge($searchCons, array("`$sfield`" => $value['value']));
                                break;
                            case 'begins':
                                $searchCons = array_merge($searchCons, array("`$sfield` like" => "$value[value]%"));
                                break;
                            case 'contains':
                                $searchCons = array_merge($searchCons, array("`$sfield` like" => "%$value[value]%"));
                                break;
                            case 'ends':
                                $searchCons = array_merge($searchCons, array("`$sfield` like" => "%$value[value]"));
                                break;
                        }
                    }
                }

            }
            $conditions = ['or' => $searchCons];
            $this->request->getSession()->write('SaasAuthGroups.searchCondition', array('or' => $searchCons));
        }

        $findOpt = array_merge($findOpt, $order);
        if (!empty($paginate)) {
            $query = $this->SaasAuthGroups->find()
                ->select($this->SaasAuthGroups)
                ->where($conditions);
            $groups = $this->paginate($query, $findOpt);
            $records = array();
            $actions = $this->__listActions();
            foreach ($groups as $k => $v) {
                $status = '';
                $bgcolor = '';
                $records[$k]['recid'] = $v['id'];
                $records[$k]['name'] = $v['name'];
                $records[$k]['home'] = $actions[$v['home']];
                $records[$k]['remark'] = $v['remark'];
            }
            $results = array('total' => $this->params['paging']['SaasAuthGroup']['count'], 'offset' => $w2Params['offset'], 'records' => $records);
            $this->set('jsonData', $results);
            $this->viewBuilder()->setLayout('txt');
            $this->render('/element/in_json');

        }
    }

    public function add() {
        $this->viewBuilder()->setLayout('simple');
        $this->loadModel('SaasAuthGroupPermissions');
        $saasAuthGroup = $this->SaasAuthGroups->newEmptyEntity();
        if(!empty($this->request->getData())) {
            $saasAuthGroup = $this->SaasAuthGroups->patchEntity($saasAuthGroup, $this->request->getData());
            $saasAuthGroup['advance_permission'] = json_encode($this->request->getData('advance'), JSON_PRETTY_PRINT);
            if($this->SaasAuthGroups->save($saasAuthGroup)) {   
                $groupId = $saasAuthGroup->id;
                if(!empty($this->request->getData('action'))){

                    $checkedActions = array();
                    foreach ($this->request->getData('action') as $k => $v) {
                        if(!empty($v)){
                            $checkedActions[] = $v;
                        }
                    }
                    if(!empty($checkedActions)){
                        foreach ($checkedActions as $v) {
                            $saveData = $this->SaasAuthGroupPermissions->newEmptyEntity();
                            $saveData['saas_auth_group_id'] = $groupId;
                            $saveData['action'] = $v;
                            $this->SaasAuthGroupPermissions->save($saveData);
                        }
                    }
                }
                $result="ok";
            } else {
                $result=__("資料儲存失敗", true);
            }
            $this->set('jsonData', array('status' => $result));
            $this->layout = 'txt';
            $this->render('/element/in_json');
        } else {
            $actionOpts = $this->__listActions();
            $this->set('menus', $this->topMenus);
            $this->set('advPermissions', $this->advPermissions);
            $this->set('advPermissionCategories', $this->advPermissionCategories);
            $this->set(compact('actionOpts', 'saasAuthGroup'));
            
        }
    }

    public function edit($id = null) {
        $this->viewBuilder()->setLayout('simple');
        $this->loadModel('SaasAuthGroupPermissions');
        $saasAuthGroup = $this->SaasAuthGroups->get($id, [
            'contain' => [],
        ]);
        if(!empty($this->request->getData())) {
            $saasAuthGroup = $this->SaasAuthGroups->patchEntity($saasAuthGroup, $this->request->getData());
            $saasAuthGroup['advance_permission'] = json_encode($this->request->getData('advance'), JSON_PRETTY_PRINT);
            if($this->SaasAuthGroups->save($saasAuthGroup)) {   
                $groupId = $saasAuthGroup->id;
                if(!empty($this->request->getData('action'))){
                    $existActions = $this->SaasAuthGroupPermissions->find('list', [
                        'keyField' => 'id',
                        'valueField' => 'action'
                    ])
                    ->where(['saas_auth_group_id'=>$groupId])
                    ->toArray();

                    $checkedActions = array();
                    foreach ($this->request->getData('action') as $k => $v) {
                        if(!empty($v)){
                            $checkedActions[] = $v;
                        }
                    }
                    
                    $addActions = array_diff($checkedActions, $existActions);
                    $delActions = array_diff($existActions, $checkedActions);

                    

                    if(!empty($addActions)){
                        foreach ($addActions as $v) {
                            $saveData = $this->SaasAuthGroupPermissions->newEmptyEntity();
                            $saveData['saas_auth_group_id'] = $groupId;
                            $saveData['action'] = $v;
                            $this->SaasAuthGroupPermissions->save($saveData);
                        }
                    }
                    
                    if(!empty($delActions)){
                        foreach ($delActions as $v) {
                            $this->SaasAuthGroupPermissions->deleteAll([
                                'saas_auth_group_id' => $groupId,
                                'action' => $v
                            ]);
                        }
                    }
                }
                $result="ok";
            } else {
                $result=__("資料儲存失敗", true);
            }
            $this->set('jsonData', array('status' => $result));
            $this->layout = 'txt';
            $this->render('/element/in_json');
        } else {
            $saasAuthGroup['action'] = $this->SaasAuthGroupPermissions->find('list', [
                'keyField' => 'id',
                'valueField' => 'action'
            ])
            ->where(['saas_auth_group_id'=>$id])
            ->toArray();
            $saasAuthGroup['advance'] = json_decode($saasAuthGroup['advance_permission'], true);
            $actionOpts = $this->__listActions();
            $this->set('menus', $this->topMenus);
            $this->set('advPermissions', $this->advPermissions);
            $this->set('advPermissionCategories', $this->advPermissionCategories);
            $this->set(compact('actionOpts', 'saasAuthGroup'));
            
        }
    }

    public function delete($id = false){
        if(!empty($id)){
            $this->loadModel("SaasAdminAuthGroups");
            $isUsedId = $this->SaasAdminAuthGroups->find('list', [
                'keyField' => 'saas_auth_group_id',
                'valueField' => 'saas_auth_group_id'
            ])
            ->where(['saas_admin_id' => $this->emUid])
            ->toArray();
            if(!in_array($id, $isUsedId)){
                $this->SaasAuthGroups->deleteAll(['id IN' => $id]);
                $this->SaasAdminAuthGroups->deleteAll(['saas_auth_group_id IN' => $id]);
                $status = 'ok';
            }else{
                $status = '不能刪除正在使用的權限群組';
            }
        }else{
            $status = 'id 不存在';
        }
        $results = array('status' => $status);
        $this->set('jsonData', $results);
        $this->viewBuilder()->setLayout('txt');
        $this->render('/element/in_json');
    }

    public function __listActions(){
        $menus = $this->topMenus;
        $subMenus = $this->menuItems;
        $actions = array();
        foreach ($menus as $k => $menu) {
            $actions[$menu['link']] = $menu['name'];
            if(!empty($subMenus[$k])){
                foreach ($subMenus[$k] as $k2 => $subMenu) {
                    $actions[$subMenu['action']] = $subMenu['name'];
                }
            }
        }
        return $actions;
    }


}
