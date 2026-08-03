<?php

namespace App\Controller;

/**
 * SongsVersions Controller
 *
 * @property \App\Model\Table\SongsVersionsTable $SongsVersions
 *
 * @method \App\Model\Entity\SongsVersion[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SongsVersionsController extends AppController
{
    #[\Override]
    public function isAuthorized($user)
    {
        $action = $this->request->getParam('action');

        if ($user['is_active']) {
            if (in_array($action, ['index', 'view', 'add', 'edit', 'display'])) {
                return true;
            }

            if (in_array($action, ['delete'])) {
                $songVersionId = (int)$this->request->getParam('pass')[0];
                if ($this->SongsVersions->isOwnedBy($songVersionId, $user['id'])) {
                    return true;
                }
            }
        } else {
            if (in_array($action, ['view', 'display'])) {
                $songVersionId = (int)$this->request->getParam('pass')[0];
                $songVersion = $this->SongsVersions->get($songVersionId);
                if (parent::isAuthorizedByShare($user, 'song', $songVersion->song_id)) {
                    return true;
                }
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
        $query = $this->SongsVersions
            ->find()
            ->contain(['Users', 'Songs']);
        $songsVersions = $this->paginate($query);

        $this->set(compact('songsVersions'));
    }

    /**
     * View method
     *
     * @param string|null $id Songs Version id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $songsVersion = $this->SongsVersions->get($id, [
            'contain' => [
                'Users',
                'Songs',
                'Comments' => ['sort' => ['Comments.created' => 'ASC']],
                'Comments.Users',
                'Files' => ['sort' => ['Files.created' => 'DESC']],
                'Files.Users',
            ],
        ]);

        $this->set('songsVersion', $songsVersion);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $songsVersion = $this->SongsVersions->newEmptyEntity();
        if ($this->request->is('post')) {
            $songsVersion = $this->SongsVersions->patchEntity($songsVersion, $this->request->getData());
            if ($this->SongsVersions->save($songsVersion)) {
                $this->Flash->success(__('The songs version has been saved.'));

                $song = $this->SongsVersions->Songs->get($songsVersion->song_id);
                $this->SongsVersions->Songs->touch($song);
                $this->SongsVersions->Songs->save($song);

                return $this->redirect(['controller' => 'Songs', 'action' => 'view', $songsVersion->song_id]);
            }
            $this->Flash->error(__('The songs version could not be saved. Please, try again.'));
        } else {
            if ($this->request->getQuery('song_id')) {
                $songsVersion->song_id = $this->request->getQuery('song_id');
            }
        }
        $users = $this->SongsVersions->Users->find('list')->limit(200);
        $songs = $this->SongsVersions->Songs->find('list')->limit(200);
        $this->set(compact('songsVersion', 'users', 'songs'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Songs Version id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $songsVersion = $this->SongsVersions->get($id, [
            'contain' => ['Songs'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $songsVersion = $this->SongsVersions->patchEntity($songsVersion, $this->request->getData());
            if ($this->SongsVersions->save($songsVersion)) {
                $this->Flash->success(__('The songs version has been saved.'));

                $this->SongsVersions->Songs->touch($songsVersion->song);
                $this->SongsVersions->Songs->save($songsVersion->song);

                return $this->redirect(['action' => 'view', $songsVersion->id]);
            }
            $this->Flash->error(__('The songs version could not be saved. Please, try again.'));
        }
        $users = $this->SongsVersions->Users->find('list')->limit(200);
        $songs = $this->SongsVersions->Songs->find('list')->limit(200);
        $this->set(compact('songsVersion', 'users', 'songs'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Songs Version id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $songsVersion = $this->SongsVersions->get($id);
        if ($this->SongsVersions->delete($songsVersion)) {
            $this->Flash->success(__('The songs version has been deleted.'));
        } else {
            $this->Flash->error(__('The songs version could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
