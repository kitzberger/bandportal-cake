<?php

namespace App\Controller;

/**
 * Comments Controller
 *
 * @property \App\Model\Table\CommentsTable $Comments
 */
class CommentsController extends AppController
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
            $commentId = (int)$this->request->getParam('pass')[0];
            if ($this->Comments->isOwnedBy($commentId, $user['id'])) {
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
        $query = $this->Comments
            ->find()
            ->contain(['Users', 'Dates', 'Ideas', 'Songs', 'SongsVersions', 'Collections'])
            ->order(['Comments.modified DESC']);
        $comments = $this->paginate($query);

        $this->set(compact('comments'));
        $this->set('_serialize', ['comments']);
    }

    /**
     * View method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $comment = $this->Comments->get($id, [
            'contain' => ['Users', 'Dates', 'Ideas', 'Songs', 'SongsVersions', 'Collections']
        ]);

        $this->set('comment', $comment);
        $this->set('_serialize', ['comment']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $comment = $this->Comments->newEmptyEntity();
        if ($this->request->is('post')) {
            $comment = $this->Comments->patchEntity($comment, $this->request->getData());
            if ($this->Comments->save($comment)) {
                $this->Flash->success(__('The comment has been saved.'));

                $redirect = ['controller' => 'comments', 'action' => 'index'];
                if ($comment->date_id) {
                    $redirect = ['controller' => 'dates', 'action' => 'view', $comment->date_id];
                }
                if ($comment->idea_id) {
                    $redirect = ['controller' => 'ideas', 'action' => 'view', $comment->idea_id];
                }
                if ($comment->song_id) {
                    $redirect = ['controller' => 'songs', 'action' => 'view', $comment->song_id];
                }
                if ($comment->song_version_id) {
                    $redirect = ['controller' => 'songsVersions', 'action' => 'view', $comment->song_version_id];
                }
                if ($comment->collection_id) {
                    $redirect = ['controller' => 'collections', 'action' => 'view', $comment->collection_id];
                }
                return $this->redirect($redirect);
            } else {
                $this->Flash->error(__('The comment could not be saved. Please, try again.'));
            }
        }
        $users = $this->Comments->Users->find('list')->limit(200)->order('username');
        $dates = $this->Comments->Dates->find('list', valueField: 'combinedTitle')->limit(200)->order('begin');
        $ideas = $this->Comments->Ideas->find('list')->limit(200)->order('title');
        $songs = $this->Comments->Songs->find('list')->limit(200)->order('title');
        $songsVersions = $this->Comments->SongsVersions->find('list', valueField: 'combinedTitle')->limit(200)->order('Songs.title')->contain(['Songs']);
        $collections = $this->Comments->Collections->find('list')->limit(200)->order('title');
        $this->set(compact('comment', 'users', 'dates', 'ideas', 'songs', 'songsVersions', 'collections'));
        $this->set('_serialize', ['comment']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $comment = $this->Comments->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $comment = $this->Comments->patchEntity($comment, $this->request->getData());
            if ($this->Comments->save($comment)) {
                $this->Flash->success(__('The comment has been saved.'));

                $redirect = ['controller' => 'comments', 'action' => 'index'];
                if ($comment->date_id) {
                    $redirect = ['controller' => 'dates', 'action' => 'view', $comment->date_id];
                }
                if ($comment->idea_id) {
                    $redirect = ['controller' => 'ideas', 'action' => 'view', $comment->idea_id];
                }
                if ($comment->song_id) {
                    $redirect = ['controller' => 'songs', 'action' => 'view', $comment->song_id];
                }
                if ($comment->song_version_id) {
                    $redirect = ['controller' => 'songsVersions', 'action' => 'view', $comment->song_version_id];
                }
                if ($comment->collection_id) {
                    $redirect = ['controller' => 'collections', 'action' => 'view', $comment->collection_id];
                }
                return $this->redirect($redirect);
            } else {
                $this->Flash->error(__('The comment could not be saved. Please, try again.'));
            }
        }
        $users = $this->Comments->Users->find('list')->limit(200)->order('username');
        $dates = $this->Comments->Dates->find('list', valueField: 'combinedTitle')->limit(200)->order('begin');
        $ideas = $this->Comments->Ideas->find('list')->limit(200)->order('title');
        $songs = $this->Comments->Songs->find('list')->limit(200)->order('title');
        $songsVersions = $this->Comments->SongsVersions->find('list', valueField: 'combinedTitle')->limit(200)->order('Songs.title')->contain(['Songs']);
        $collections = $this->Comments->Collections->find('list')->limit(200)->order('title');
        $this->set(compact('comment', 'users', 'dates', 'ideas', 'songs', 'songsVersions', 'collections'));
        $this->set('_serialize', ['comment']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $comment = $this->Comments->get($id);
        if ($this->Comments->delete($comment)) {
            $this->Flash->success(__('The comment has been deleted.'));
        } else {
            $this->Flash->error(__('The comment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
