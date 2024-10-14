<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * SyncLogs Controller
 *
 * @property \App\Model\Table\SyncLogsTable $SyncLogs
 * @method \App\Model\Entity\SyncLog[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SyncLogsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['SyncRecords'],
        ];
        $syncLogs = $this->paginate($this->SyncLogs);

        $this->set(compact('syncLogs'));
    }

    /**
     * View method
     *
     * @param string|null $id Sync Log id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $syncLog = $this->SyncLogs->get($id, [
            'contain' => ['SyncRecords'],
        ]);

        $this->set(compact('syncLog'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $syncLog = $this->SyncLogs->newEmptyEntity();
        if ($this->request->is('post')) {
            $syncLog = $this->SyncLogs->patchEntity($syncLog, $this->request->getData());
            if ($this->SyncLogs->save($syncLog)) {
                $this->Flash->success(__('The sync log has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sync log could not be saved. Please, try again.'));
        }
        $syncRecords = $this->SyncLogs->SyncRecords->find('list', ['limit' => 200])->all();
        $this->set(compact('syncLog', 'syncRecords'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Sync Log id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $syncLog = $this->SyncLogs->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $syncLog = $this->SyncLogs->patchEntity($syncLog, $this->request->getData());
            if ($this->SyncLogs->save($syncLog)) {
                $this->Flash->success(__('The sync log has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sync log could not be saved. Please, try again.'));
        }
        $syncRecords = $this->SyncLogs->SyncRecords->find('list', ['limit' => 200])->all();
        $this->set(compact('syncLog', 'syncRecords'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Sync Log id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $syncLog = $this->SyncLogs->get($id);
        if ($this->SyncLogs->delete($syncLog)) {
            $this->Flash->success(__('The sync log has been deleted.'));
        } else {
            $this->Flash->error(__('The sync log could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
