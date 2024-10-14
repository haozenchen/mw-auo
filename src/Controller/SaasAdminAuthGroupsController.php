<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * SaasAdminAuthGroups Controller
 *
 * @property \App\Model\Table\SaasAdminAuthGroupsTable $SaasAdminAuthGroups
 * @method \App\Model\Entity\SaasAdminAuthGroup[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SaasAdminAuthGroupsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['SaasAdmins', 'SaasAuthGroups'],
        ];
        $saasAdminAuthGroups = $this->paginate($this->SaasAdminAuthGroups);

        $this->set(compact('saasAdminAuthGroups'));
    }

    /**
     * View method
     *
     * @param string|null $id Saas Admin Auth Group id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $saasAdminAuthGroup = $this->SaasAdminAuthGroups->get($id, [
            'contain' => ['SaasAdmins', 'SaasAuthGroups'],
        ]);

        $this->set(compact('saasAdminAuthGroup'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $saasAdminAuthGroup = $this->SaasAdminAuthGroups->newEmptyEntity();
        if ($this->request->is('post')) {
            $saasAdminAuthGroup = $this->SaasAdminAuthGroups->patchEntity($saasAdminAuthGroup, $this->request->getData());
            if ($this->SaasAdminAuthGroups->save($saasAdminAuthGroup)) {
                $this->Flash->success(__('The saas admin auth group has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The saas admin auth group could not be saved. Please, try again.'));
        }
        $saasAdmins = $this->SaasAdminAuthGroups->SaasAdmins->find('list', ['limit' => 200])->all();
        $saasAuthGroups = $this->SaasAdminAuthGroups->SaasAuthGroups->find('list', ['limit' => 200])->all();
        $this->set(compact('saasAdminAuthGroup', 'saasAdmins', 'saasAuthGroups'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Saas Admin Auth Group id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $saasAdminAuthGroup = $this->SaasAdminAuthGroups->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $saasAdminAuthGroup = $this->SaasAdminAuthGroups->patchEntity($saasAdminAuthGroup, $this->request->getData());
            if ($this->SaasAdminAuthGroups->save($saasAdminAuthGroup)) {
                $this->Flash->success(__('The saas admin auth group has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The saas admin auth group could not be saved. Please, try again.'));
        }
        $saasAdmins = $this->SaasAdminAuthGroups->SaasAdmins->find('list', ['limit' => 200])->all();
        $saasAuthGroups = $this->SaasAdminAuthGroups->SaasAuthGroups->find('list', ['limit' => 200])->all();
        $this->set(compact('saasAdminAuthGroup', 'saasAdmins', 'saasAuthGroups'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Saas Admin Auth Group id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $saasAdminAuthGroup = $this->SaasAdminAuthGroups->get($id);
        if ($this->SaasAdminAuthGroups->delete($saasAdminAuthGroup)) {
            $this->Flash->success(__('The saas admin auth group has been deleted.'));
        } else {
            $this->Flash->error(__('The saas admin auth group could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
