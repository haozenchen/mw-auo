<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * SaasLoginRecords Controller
 *
 * @property \App\Model\Table\SaasLoginRecordsTable $SaasLoginRecords
 * @method \App\Model\Entity\SaasLoginRecord[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SaasLoginRecordsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['SaasAdmins'],
        ];
        $saasLoginRecords = $this->paginate($this->SaasLoginRecords);

        $this->set(compact('saasLoginRecords'));
    }

    /**
     * View method
     *
     * @param string|null $id Saas Login Record id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $saasLoginRecord = $this->SaasLoginRecords->get($id, [
            'contain' => ['SaasAdmins'],
        ]);

        $this->set(compact('saasLoginRecord'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $saasLoginRecord = $this->SaasLoginRecords->newEmptyEntity();
        if ($this->request->is('post')) {
            $saasLoginRecord = $this->SaasLoginRecords->patchEntity($saasLoginRecord, $this->request->getData());
            if ($this->SaasLoginRecords->save($saasLoginRecord)) {
                $this->Flash->success(__('The saas login record has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The saas login record could not be saved. Please, try again.'));
        }
        $saasAdmins = $this->SaasLoginRecords->SaasAdmins->find('list', ['limit' => 200])->all();
        $this->set(compact('saasLoginRecord', 'saasAdmins'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Saas Login Record id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $saasLoginRecord = $this->SaasLoginRecords->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $saasLoginRecord = $this->SaasLoginRecords->patchEntity($saasLoginRecord, $this->request->getData());
            if ($this->SaasLoginRecords->save($saasLoginRecord)) {
                $this->Flash->success(__('The saas login record has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The saas login record could not be saved. Please, try again.'));
        }
        $saasAdmins = $this->SaasLoginRecords->SaasAdmins->find('list', ['limit' => 200])->all();
        $this->set(compact('saasLoginRecord', 'saasAdmins'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Saas Login Record id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $saasLoginRecord = $this->SaasLoginRecords->get($id);
        if ($this->SaasLoginRecords->delete($saasLoginRecord)) {
            $this->Flash->success(__('The saas login record has been deleted.'));
        } else {
            $this->Flash->error(__('The saas login record could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
