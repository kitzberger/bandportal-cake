<?php

namespace App\Controller;

/**
 * Songs Controller
 *
 * @property \App\Model\Table\SongsTable $Songs
 */
class SongsController extends AppController
{
    #[\Override]
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Github');
    }

    #[\Override]
    public function isAuthorized($user)
    {
        $action = $this->request->getParam('action');

        if ($user['is_active']) {
            if (in_array($action, ['index', 'view', 'add', 'display', 'edit'])) {
                return true;
            }

            if (in_array($action, ['delete'])) {
                $songId = (int)$this->request->getParam('pass')[0];
                if ($this->Songs->isOwnedBy($songId, $user['id'])) {
                    return true;
                }
            }
        } else {
            if (in_array($action, ['view', 'display'])) {
                if (parent::isAuthorizedByShare($user, 'song')) {
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
        $sword = $this->request->getQuery('sword');

        $query = $this->Songs
            ->find()
            ->contain(['Users', 'SongsVersions'])
            ->order(['Songs.modified DESC'])
            ->where([
                'OR' => [
                    'Songs.title LIKE' => '%' . $sword . '%',
                    'Songs.artist LIKE' => '%' . $sword . '%',
                ],
            ]);
        $songs = $this->paginate($query);

        $this->set(compact('songs', 'sword'));
        $this->set('_serialize', ['songs']);
    }

    /**
     * View method
     *
     * @param string|null $id Song id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $song = $this->Songs->get($id, [
            'contain' => [
                'Users',
                'Comments' => ['sort' => ['Comments.created' => 'ASC']],
                'Comments.Users',
                'Files' => ['sort' => ['Files.created' => 'DESC']],
                'Files.Users',
                'Files.Collections',
                'Collections.Users',
                'SongsVersions',
                'SongsVersions.Files' => ['conditions' => ['is_public' => 1]],
            ]
        ]);

        $this->set('song', $song);
        $this->set('_serialize', ['song']);
    }

    /**
     * Render method
     *
     * @param string|null $id Song id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function display($id = null)
    {
        $song = $this->Songs->get($id);

        $this->set('transposeBy', $this->request->getQuery('transposeBy') ?? 'off');
        $this->set('mode', $this->request->getQuery('mode') ?? 'full');

        $zoom = (float)($this->request->getQuery('zoom') ?? 1.0);

        $this->viewBuilder()->setOption(
            'pdfConfig',
            [
                'title' => $song->title,
                'engine' => [
                    'options' => [
                        'zoom' => $zoom,
                    ],
                ],
            ]
        );

        $this->set('song', $song);
        $this->set('_serialize', ['song']);
    }

    /**
     * Sync method
     *
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function sync()
    {
        if ($this->enabledFeatures['githubRepo']) {
            $songs = $this->Songs->find('all')->where(['Songs.url !=' => '']);

            $count = 0;
            foreach ($songs as $song) {
                if ($song['url']) {
                    $songText = $this->Github->loadResource($song['url']);
                    if ($songText && $songText != $song['text']) {
                        $song = $this->Songs->patchEntity($song, ['text' => $songText]);
                        if ($this->Songs->save($song)) {
                            $this->Flash->success(sprintf(__('The song "{0}" has been updated.', $song['title'])));
                            $count++;
                        }
                    }
                }
            }

            if ($count === 0) {
                $this->Flash->default(__('No songs updated!'));
            }
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $song = $this->Songs->newEmptyEntity();
        if ($this->request->is('post')) {
            $song = $this->Songs->patchEntity($song, $this->request->getData());
            if ($this->Songs->save($song)) {
                $this->Flash->success(__('The song has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The song could not be saved. Please, try again.'));
            }
        }
        $users = $this->Songs->Users->find('list', ['limit' => 200]);
        $this->set(compact('song', 'users'));
        $this->set('_serialize', ['song']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Song id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $song = $this->Songs->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $song = $this->Songs->patchEntity($song, $this->request->getData());
            if ($this->Songs->save($song)) {
                $this->Flash->success(__('The song has been saved.'));

                $returnUrl = $this->request->getQuery('returnUrl');
                return $this->redirect($returnUrl ?: ['action' => 'index']);
            } else {
                $this->Flash->error(__('The song could not be saved. Please, try again.'));
            }
        }
        $users = $this->Songs->Users->find('list', ['limit' => 200]);
        $this->set(compact('song', 'users'));
        $this->set('_serialize', ['song']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Song id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $song = $this->Songs->get($id);
        if ($this->Songs->delete($song)) {
            $this->Flash->success(__('The song has been deleted.'));
        } else {
            $this->Flash->error(__('The song could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
