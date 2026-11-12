<?php

namespace App\Controller;

/**
 * Votes Controller
 *
 * @property \App\Model\Table\VotesTable $Votes
 */
class VotesController extends AppController
{
    #[\Override]
    public function isAuthorized($user)
    {
        $action = $this->request->getParam('action');

        if ($user['is_active']) {
            if (in_array($action, ['index', 'view', 'add'])) {
                return true;
            }
        } else {
            if (in_array($action, ['add'])) {
                foreach (['date', 'idea', 'song'] as $type) {
                    $recordId = $this->request->getData($type . '_id');
                    if (!empty($recordId)) {
                        if (parent::isAuthorizedByShare($user, $type, $recordId)) {
                            return true;
                        }
                    }
                }
            }
        }

        if (in_array($action, ['edit', 'delete'])) {
            $voteId = (int)$this->request->getParam('pass')[0];
            if ($this->Votes->isOwnedBy($voteId, $user['id'])) {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $query = $this->Votes
            ->find()
            ->contain(['Users', 'Dates', 'Ideas']);
        $votes = $this->paginate($query, ['order' => ['Votes.modified' => 'DESC']]);

        $this->set(compact('votes'));
        $this->set('_serialize', ['votes']);
    }

    /**
     * View method
     *
     * @param string|null $id Vote id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $vote = $this->Votes->get($id, contain: ['Users', 'Dates', 'Ideas']);

        $this->set('vote', $vote);
        $this->set('_serialize', ['vote']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $vote = $this->Votes->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $previousVote = $this->Votes->find()
                ->where([
                    'user_id' => $data['user_id'] ?? '',
                    'OR' => [
                        'idea_id' => $data['idea_id'] ?? '',
                        'date_id' => $data['date_id'] ?? '',
                    ]
                ])
                ->first();
            if ($previousVote) {
                $vote = $this->Votes->patchEntity($previousVote, $data);
            } else {
                $vote = $this->Votes->patchEntity($vote, $data);
            }
            if ($this->Votes->save($vote)) {
                $vote = $this->Votes->get($vote['id'], contain: ['Users', 'Dates', 'Ideas', 'Dates.Votes', 'Ideas.Votes']);
                $usersTable = $this->fetchTable('Users');
                $users = $usersTable->find('all');
                if ($this->request->is('ajax')) {
                    $this->set('vote', $vote);
                    $this->viewBuilder()->setClassName('Json');
                    $this->viewBuilder()->setOption('serialize', ['vote']);

                    return;
                } else {
                    $this->Flash->success(__('The vote has been saved.'));
                    return $this->redirect(['action' => 'index']);
                }

            } else {
                $this->Flash->error(__('The vote could not be saved. Please, try again.'));
            }
        }
        $users = $this->Votes->Users->find('list')->limit(200)->orderBy('username');
        $dates = $this->Votes->Dates->find('list', valueField: 'combinedTitle')->limit(200)->orderBy('begin');
        $ideas = $this->Votes->Ideas->find('list')->limit(200)->orderBy('title');
        $this->set(compact('vote', 'users', 'dates', 'ideas'));
        $this->viewBuilder()->setOption('serialize', ['vote']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Vote id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $vote = $this->Votes->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $vote = $this->Votes->patchEntity($vote, $this->request->getData());
            if ($this->Votes->save($vote)) {
                $vote = $this->Votes->get($vote['id'], contain: ['Users', 'Dates', 'Ideas']);

                if ($this->request->is('ajax')) {
                    $this->set('vote', $vote);
                    $this->viewBuilder()->setClassName('Json');
                    $this->viewBuilder()->setOption('serialize', ['vote']);

                    return;
                } else {
                    $this->Flash->success(__('The vote has been saved.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error(__('The vote could not be saved. Please, try again.'));
            }
        }
        $users = $this->Votes->Users->find('list')->limit(200)->orderBy('username');
        $dates = $this->Votes->Dates->find('list', valueField: 'combinedTitle')->limit(200)->orderBy('begin DESC');
        $ideas = $this->Votes->Ideas->find('list')->limit(200)->orderBy('title');
        $this->set(compact('vote', 'users', 'dates', 'ideas'));
        $this->viewBuilder()->setOption('serialize', ['vote']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Vote id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $vote = $this->Votes->get($id);
        if ($this->Votes->delete($vote)) {
            $this->Flash->success(__('The vote has been deleted.'));
        } else {
            $this->Flash->error(__('The vote could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
