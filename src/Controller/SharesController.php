<?php

namespace App\Controller;

/**
 * Shares Controller
 *
 * @property \App\Model\Table\SharesTable $Shares
 *
 * @method \App\Model\Entity\Share[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SharesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $query = $this->Shares
            ->find()
            ->contain(['Users', 'Dates', 'Ideas', 'Songs', 'Collections', 'Files']);
        $shares = $this->paginate($query);

        $this->set(compact('shares'));
    }

    /**
     * View method
     *
     * @param string|null $id Share id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $share = $this->Shares->get($id, [
            'contain' => ['Users', 'Dates', 'Ideas', 'Songs', 'Collections', 'Files']
        ]);

        $this->set('share', $share);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $share = $this->Shares->newEmptyEntity();
        if ($this->request->is('post')) {
            $share = $this->Shares->patchEntity($share, $this->request->getData());
            if ($this->Shares->save($share)) {
                $this->Flash->success(__('The share has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The share could not be saved. Please, try again.'));
        }
        $users = $this->Shares->Users->find('list')->limit(9999)->orderBy('username');
        $dates = $this->Shares->Dates->find('list', valueField: 'combinedTitle')->limit(9999)->orderBy('begin DESC');
        $ideas = $this->Shares->Ideas->find('list')->limit(9999)->orderBy('title');
        $songs = $this->Shares->Songs->find('list')->limit(9999)->orderBy('title');
        $collections = $this->Shares->Collections->find('list')->limit(9999)->orderBy('title');
        $files = $this->Shares->Files->find('list')->limit(9999);
        $this->set(compact('share', 'users', 'dates', 'ideas', 'songs', 'collections', 'files'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Share id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $share = $this->Shares->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $share = $this->Shares->patchEntity($share, $this->request->getData());
            if ($this->Shares->save($share)) {
                $this->Flash->success(__('The share has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The share could not be saved. Please, try again.'));
        }
        $users = $this->Shares->Users->find('list')->limit(9999)->orderBy('username');
        $dates = $this->Shares->Dates->find('list', valueField: 'combinedTitle')->limit(9999)->orderBy('begin DESC');
        $ideas = $this->Shares->Ideas->find('list')->limit(9999)->orderBy('title');
        $songs = $this->Shares->Songs->find('list')->limit(9999)->orderBy('title');
        $collections = $this->Shares->Collections->find('list')->limit(9999)->orderBy('title');
        $files = $this->Shares->Files->find('list')->limit(9999)->orderBy('title');
        $this->set(compact('share', 'users', 'dates', 'ideas', 'songs', 'collections', 'files'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Share id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $share = $this->Shares->get($id);
        if ($this->Shares->delete($share)) {
            $this->Flash->success(__('The share has been deleted.'));
        } else {
            $this->Flash->error(__('The share could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
