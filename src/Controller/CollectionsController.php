<?php

namespace App\Controller;

use App\Model\Entity\Collection;

/**
 * Collections Controller
 *
 * @property \App\Model\Table\CollectionsTable $Collections
 */
class CollectionsController extends AppController
{
    #[\Override]
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
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $sword = $this->request->getQuery('sword');

        $query = $this->Collections
            ->find()
            ->contain([
                'Users',
                'Files',
                'Songs' => fn(\Cake\ORM\Query $q) => $q->where(['is_pseudo' => false]),
            ])
            ->where(['Collections.title LIKE' => '%' . $sword . '%']);
        $collections = $this->paginate($query, ['order' => ['Collections.modified' => 'DESC']]);

        $this->set(compact('collections', 'sword'));
        $this->set('_serialize', ['collections']);
    }

    /**
     * View method
     *
     * @param string|null $id Collection id.
     * @return \Cake\Http\Response|null
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

        $usersTable = $this->fetchTable('Users');
        $passiveUsers = $usersTable->find()
            ->where(['Users.is_passive' => true]);

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
     * @return \Cake\Http\Response|null
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
            $collection->isNew();
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
        $users = $this->Collections->Users->find('list')->limit(200);
        $files = $this->getOrderedAssociationList('Files', $collection, false);
        $songs = $this->getOrderedAssociationList('Songs', $collection);
        $this->set(compact('collection', 'users', 'files', 'songs'));
        $this->set('_serialize', ['collection']);
    }

    /**
     * Set version for a song within a collection
     *
     * @return \Cake\Http\Response|void Redirects on successful add, renders view otherwise.
     */
    public function setSongVersion()
    {
        $collectionsSongsTable = $this->fetchTable('CollectionsSongs');

        $collectionSong = $collectionsSongsTable
            ->find('all')
            ->where([
                'collection_id' => $this->request->getData('collection_id'),
                'song_id' => $this->request->getData('song_id'),
            ])
            ->limit(1)->first();

        if (is_null($collectionSong)) {
            $collectionSong = $collectionsSongsTable->newEmptyEntity();
        }

        if ($this->request->is('post')) {
            $collectionSong = $collectionsSongsTable->patchEntity($collectionSong, $this->request->getData());
            if ($collectionsSongsTable->save($collectionSong)) {
                if ($this->request->is('ajax')) {
                    $this->set('collectionSong', $collectionSong);
                    $this->set('_serialize', ['collectionSong']);

                    return;
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
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
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
        $users = $this->Collections->Users->find('list')->limit(200);
        $files = $this->getOrderedAssociationList('Files', $collection, false);
        $songs = $this->getOrderedAssociationList('Songs', $collection);
        $this->set(compact('collection', 'users', 'files', 'songs'));
        $this->set('_serialize', ['collection']);
    }

    /**
     * Build an ordered list for an association, placing the collection's existing items first.
     */
    private function getOrderedAssociationList(string $association, Collection $collection, bool $showAll = true): array
    {
        $field = strtolower($association);
        $all = $this->Collections->{$association}->find('list')->limit(200)->orderBy('title ASC')->toArray();

        if (empty($collection->{$field})) {
            return $showAll ? $all : [];
        }

        $ordered = [];
        foreach ($collection->{$field} as $entity) {
            $ordered[$entity->id] = $entity->title;
        }

        return $ordered + ($showAll ? $all : []);
    }

    /**
     * Delete method
     *
     * @param string|null $id Collection id.
     * @return \Cake\Http\Response|null Redirects to index.
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
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function share($collection = null, $user = null, $template = 'share')
    {
        $usersTable = $this->fetchTable('Users');
        $sharesTable = $this->fetchTable('Shares');

        $collection = $this->Collections->get($collection, []);
        $user = $usersTable->get($user, [
            'conditions' => [
                'Users.is_passive' => true,
            ],
        ]);

        $subject = __('A collection is now shared with you!');
        $message = 'We\'d like to show you this collection here ;-)';

        if ($user) {
            if (!$sharesTable->sharedWithUser('collection', $collection->id, $user['id'])) {
                $share = $sharesTable->newEmptyEntity();
                $share->user = $user;
                $share->collection = $collection;
                $sharesTable->save($share);
            }

            $this->sendMail($subject, $message, $user->email, $template, ['user' => $user, 'collection' => $collection]);
            $this->Flash->success(__('The user has been informed.'));
        } else {
            $this->Flash->error(__('The user could not be informed. Please, try again.'));
        }

        return $this->redirect(['action' => 'view', $collection->id]);
    }
}
