<?php

namespace App\Controller;

/**
 * Collections Controller
 *
 * @property \App\Model\Table\CollectionsTable $Collections
 */
class CollectionsController extends AppController
{
    public function isAuthorized($user)
    {
        $action = $this->request->getParam('action');

        if ($user['is_active']) {
            if (in_array($action, ['index', 'view', 'add', 'edit', 'share', 'setSongVersion'])) {
                return true;
            }

            if (in_array($action, ['delete'])) {
                $collectionId = (int)$this->request->getParam('pass')[0];
                if ($this->Collections->isOwnedBy($collectionId, $user['id'])) {
                    return true;
                }
            }
        } else {
            if (in_array($action, ['view'])) {
                if (parent::isAuthorizedByShare($user, 'collection')) {
                    return true;
                }
            }
        }

        return parent::isAuthorized($user);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $sword = $this->request->getQuery('sword');

        $this->paginate = [
            'contain' => [
                'Users',
                'Files',
                'Songs' => function (\Cake\ORM\Query $q) {
                    return $q->find('all', ['conditions' => ['is_pseudo' => false]]);
                },
            ],
            'order' => ['Collections.modified DESC'],
            'conditions' => ['Collections.title LIKE' => '%' . $sword . '%'],
        ];
        $collections = $this->paginate($this->Collections);

        $this->set(compact('collections', 'sword'));
        $this->set('_serialize', ['collections']);
    }

    /**
     * View method
     *
     * @param string|null $id Collection id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $collection = $this->Collections->get($id, [
            'contain' => [
                'Users',
                'Files',
                'Files.Users',
                'Files.Songs',
                'Files.Ideas',
                'Files.Dates',
                'Songs',
                'Songs.Files' => [
                    'conditions' => ['is_public' => 1],
                    'sort' => ['Files.created' => 'DESC']
                ],
                'Songs.Users',
                'Songs.SongsVersions',
                'Songs.SongsVersions.Files' => [
                    'conditions' => ['is_public' => 1],
                    'sort' => ['Files.created' => 'DESC']
                ],
                'Shares',
                'Shares.Users',
                'Comments' => ['sort' => ['Comments.modified' => 'ASC']],
                'Comments.Users',
            ]
        ]);

        $this->loadModel('Users');
        $passiveUsers = $this->Users->find(
            'all',
            [
                'conditions' => [
                    'Users.is_passive' => true,
                ],
            ]
        );

        $zoom = (float)($this->request->getQuery('zoom') ?? 1.0);

        $this->viewBuilder()->setOption(
            'pdfConfig',
            [
                'title' => $collection->title,
                'engine' => [
                    'options' => [
                        'zoom' => $zoom,
                    ],
                ],
            ]
        );

        $this->set('collection', $collection);
        $this->set('passiveUsers', $passiveUsers);
        $this->set('_serialize', ['collection']);
    }

    /**
     * Add/copy method
     *
     * @param string|null $id Collection id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function add($id = null)
    {
        if (is_null($id)) {
            $collection = $this->Collections->newEmptyEntity();
        } else {
            $collection = $this->Collections->get($id, [
                'contain' => [
                    'Files' => ['sort' => 'sorting ASC'],
                    'Songs' => ['sort' => 'sorting ASC'],
                ]
            ]);
            $collection->isNew(true);
            $collection->id = null;
            $collection->user_id = null;
            $collection->title .= ' (copy)';
            $collection->created = null;
            $collection->modified = null;
        }
        if ($this->request->is('post')) {
            $collection = $this->Collections->patchEntity($collection, $this->request->getData());
            if ($this->Collections->save($collection)) {
                $this->Flash->success(__('The collection has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The collection could not be saved. Please, try again.'));
            }
        }
        $users = $this->Collections->Users->find('list', ['limit' => 200]);
        $files = $this->Collections->Files->find('list', ['limit' => 200]);
        $songs = $this->Collections->Songs->find('list', ['limit' => 200]);
        $this->set(compact('collection', 'users', 'files', 'songs'));
        $this->set('_serialize', ['collection']);
    }

    /**
     * Set version for a song within a collection
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function setSongVersion()
    {
        $this->loadModel('CollectionsSongs');

        $collectionSong = $this->CollectionsSongs
            ->find('all')
            ->where([
                'collection_id' => $this->request->getData('collection_id'),
                'song_id' => $this->request->getData('song_id'),
            ])
            ->limit(1)->first();

        if (is_null($collectionSong)) {
            $collectionSong = $this->CollectionsSongs->newEmptyEntity();
        }

        if ($this->request->is('post')) {
            $collectionSong = $this->CollectionsSongs->patchEntity($collectionSong, $this->request->getData());
            if ($this->CollectionsSongs->save($collectionSong)) {
                if ($this->request->is('ajax')) {
                    if ($this->request->is('json')) {
                    }
                    // no return, so the json rendering kicks in.
                } else {
                    $this->Flash->success(__('The version has been set.'));
                    return $this->redirect(['action' => 'view', $collectionSong->collection_id]);
                }
            } else {
                $this->Flash->error(__('The version could not be set. Please, try again.'));
            }
        }

        $this->set(compact('collectionSong'));
        $this->set('_serialize', ['collectionSong']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Collection id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $collection = $this->Collections->get($id, [
            'contain' => [
                'Files' => ['sort' => 'sorting ASC'],
                'Songs' => ['sort' => 'sorting ASC'],
            ]
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $collection = $this->Collections->patchEntity(
                $collection,
                $this->request->getData(),
                [
                    'associated' => [
                        'Files',
                        'Files._joinData',
                        'Songs',
                        'Songs._joinData',
                    ]
                ]
            );
            if ($this->Collections->save($collection)) {
                $this->Flash->success(__('The collection has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The collection could not be saved. Please, try again.'));
            }
        }
        $users = $this->Collections->Users->find('list', ['limit' => 200]);

        // Create a list of all files, but start with the files that are listed in this collection
        $allFiles = $this->Collections->Files->find('list', ['limit' => 200, 'order' => 'title ASC']);
        $files = [];
        foreach ($collection->files as $file) {
            $files[$file->id] = $file->title;
        }
        $files += $allFiles->toArray();

        // Create a list of all songs, but start with the songs that are listed in this collection
        $allSongs = $this->Collections->Songs->find('list', ['limit' => 200, 'order' => 'title ASC']);
        $songs = [];
        foreach ($collection->songs as $song) {
            $songs[$song->id] = $song->title;
        }
        $songs += $allSongs->toArray();

        $this->set(compact('collection', 'users', 'files', 'songs'));
        $this->set('_serialize', ['collection']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Collection id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $collection = $this->Collections->get($id);
        if ($this->Collections->delete($collection)) {
            $this->Flash->success(__('The collection has been deleted.'));
        } else {
            $this->Flash->error(__('The collection could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Shares a collection with a passive user.
     *
     * @param string|null $collection Collection id.
     * @param string|null $user User id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function share($collection = null, $user = null, $template = 'share')
    {
        $this->loadModel('Users');
        $this->loadModel('Shares');

        $collection = $this->Collections->get($collection, []);
        $user = $this->Users->get($user, [
            'conditions' => [
                'Users.is_passive' => true,
            ],
        ]);

        $subject = __('A collection is now shared with you!');
        $message = 'We\'d like to show you this collection here ;-)';

        if ($user) {
            if (!$this->Shares->sharedWithUser('collection', $collection->id, $user['id'])) {
                $share = $this->Shares->newEmptyEntity();
                $share->user = $user;
                $share->collection = $collection;
                $this->Shares->save($share);
            }

            $this->sendMail($subject, $message, $user->email, $template, ['user' => $user, 'collection' => $collection]);
            $this->Flash->success(__('The user has been informed.'));
        } else {
            $this->Flash->error(__('The user could not be informed. Please, try again.'));
        }

        return $this->redirect(['action' => 'view', $collection->id]);
    }
}
