<?php

namespace App\Controller;

/**
 * Files Controller
 *
 * @property \App\Model\Table\FilesTable $Files
 */
class FilesController extends AppController
{
    public function isAuthorized($user)
    {
        $action = $this->request->getParam('action');

        if ($user['is_active']) {
            if (in_array($action, ['index', 'view', 'add', 'edit', 'upload'])) {
                return true;
            }

            if (in_array($action, ['delete'])) {
                $fileId = (int)$this->request->getParam('pass')[0];
                if ($this->Files->isOwnedBy($fileId, $user['id'])) {
                    return true;
                }
            }
        } else {
            if (in_array($action, ['view'])) {
                if (parent::isAuthorizedByShare($user, 'file')) {
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
            'contain' => ['Users', 'Dates', 'Ideas', 'Songs', 'SongsVersions', 'Collections'],
            'order' => ['Files.modified DESC'],
            'conditions' => ['Files.title LIKE' => '%' . $sword . '%'],
        ];
        $files = $this->paginate($this->Files);

        $this->set(compact('files', 'sword'));
        $this->set('_serialize', ['files']);
    }

    /**
     * View method
     *
     * @param string|null $id File id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $file = $this->Files->get($id, [
            'contain' => ['Users', 'Dates', 'Ideas', 'Songs', 'SongsVersions', 'Collections', 'Collections.Users']
        ]);

        $this->set('file', $file);
        $this->set('_serialize', ['file']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $file = $this->Files->newEmptyEntity();

        if ($this->request->is('post')) {
            try {
                $filename = $this->storeFile();
            } catch (\Exception $e) {
                $filename = false;
                $this->Flash->error(__('File upload failed! Message:') . ' ' . $e->getMessage());
            }

            if ($filename) {
                $data = $this->request->getData();
                $data['file'] = $filename;
                $file = $this->Files->patchEntity($file, $data);
                if ($this->Files->save($file)) {
                    $this->Flash->success(__('The file has been saved.'));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('The file could not be saved. Please, try again.'));
                }
            }
        }
        $users = $this->Files->Users->find('list', ['limit' => 100, 'order' => 'username']);
        $dates = $this->Files->Dates->find('list', ['limit' => 100, 'order' => 'begin DESC', 'valueField' => 'combinedTitle']);
        $ideas = $this->Files->Ideas->find('list', ['limit' => 200, 'order' => 'title']);
        $collections = $this->Files->Collections->find('list', ['limit' => 200, 'order' => 'title']);
        $songs = $this->Files->Songs->find('list', ['limit' => 200, 'order' => 'title']);
        $this->set(compact('file', 'users', 'dates', 'ideas', 'songs', 'collections'));
        $this->set('_serialize', ['file']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function upload()
    {
        $file = $this->Files->newEmptyEntity();

        if ($this->request->is('post')) {
            try {
                $filename = $this->storeFile();
            } catch (\Exception $e) {
                $filename = false;
                $this->set('error', __('File upload failed! Message:') . ' ' . $e->getMessage());
                $this->response->header('HTTP/1.1 500', 'Internal Server Error');
            }

            if ($filename) {
                $data = $this->request->getData();
                $data['file'] = $filename;
                $data['title'] = $filename;
                $file = $this->Files->patchEntity($file, $data);
                if ($this->Files->save($file)) {
                    $this->set('file', $file->id);
                    if (isset($data['collection_id'])) {
                        $collectionsFilesTable = $this->fetchTable('CollectionsFiles');
                        $collectionsFile = $collectionsFilesTable->newEmptyEntity();
                        $collectionsFile->file_id = $file->id;
                        $collectionsFile->collection_id = $data['collection_id'];
                        $collectionsFile->sorting = 0;
                        $collectionsFilesTable->save($collectionsFile);

                        if ($file->isAudio()) {
                            // suggest songs that this file might belong to
                            $songsTable = $this->fetchTable('Songs');
                            $songs = $songsTable->find('all')->toArray();
                            $this->set('suggestions', ['songs' => $songs]);
                        }
                    }
                } else {
                    $this->set('error', __('The file could not be saved. Please, try again.'));
                    $this->response->header('HTTP/1.1 500', 'Internal Server Error');
                }
            }
        }

        $this->set('_serialize', ['file', 'error', 'suggestions']);
    }

    /**
     * Edit method
     *
     * @param string|null $id File id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $file = $this->Files->get($id, [
            'contain' => ['Collections']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $file = $this->Files->patchEntity($file, $this->request->getData());
            if ($this->Files->save($file)) {
                $this->Flash->success(__('The file has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The file could not be saved. Please, try again.'));
            }
        }
        $users = $this->Files->Users->find('list', ['limit' => 200, 'order' => 'username']);
        $dates = $this->Files->Dates->find('list', ['limit' => 200, 'order' => 'begin DESC', 'valueField' => 'combinedTitle']);
        $ideas = $this->Files->Ideas->find('list', ['limit' => 200, 'order' => 'title']);
        $collections = $this->Files->Collections->find('list', ['limit' => 200, 'order' => 'title']);
        $songs = $songsVersions = [];
        if ($file->isAudio()) {
            $songs = $this->Files->Songs->find('list', ['limit' => 200, 'order' => 'title']);
            $songsVersions = $this->Files->SongsVersions->find(
                'list',
                [
                    'limit' => 200,
                    'order' => 'Songs.title',
                    'valueField' => 'combinedTitle',
                ]
            )->contain(['Songs']);
            if ($file->song_id) {
                $songsVersions->where(['song_id' => $file->song_id]);
            }
        }
        $this->set(compact('file', 'users', 'dates', 'ideas', 'songs', 'songsVersions', 'collections'));
        $this->set('_serialize', ['file']);
    }

    /**
     * @param string $fileVariable a key of $_FILES
     * @return string
     * @throws \Exception
     */
    protected function storeFile($fileVariable = 'file')
    {
        $targetFolder = 'uploads' . DS;

        $errors = [];

        if (empty($_FILES)) {
            $errors[] = 'No file given!';
        } else {
            $tempFile = $_FILES[$fileVariable]['tmp_name'];
            $success = true;

            $filename = (string)$_FILES[$fileVariable]['name'];
            $rawBaseName = pathinfo($filename, PATHINFO_FILENAME);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $filename = $rawBaseName . '.' . $extension;

            // find a name that's still available by counting up until available filename found
            $counter = 0;
            $targetFile =  $targetFolder . $rawBaseName . '.' . $extension;
            while (file_exists($targetFile)) {
                $counter++;
                $filename = $rawBaseName . '-' . $counter . '.' . $extension;
                $targetFile =  $targetFolder . $filename;
                if ($counter > 99) {
                    $errors[] = 'Too many iterations when finding a good filename!';
                    break;
                }
            }

            // no errors so far? -> move file from tmp folder to target folder
            if (empty($errors)) {
                if (!move_uploaded_file($tempFile, WWW_ROOT . $targetFile)) {
                    $errors[] = 'Moving file failed!';
                }
            }
        }

        if (count($errors)) {
            throw new \Exception(implode(', ', $errors));
        } else {
            return $filename;
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id File id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $file = $this->Files->get($id);
        if ($this->Files->delete($file)) {
            $this->Flash->success(__('The file has been deleted.'));
        } else {
            $this->Flash->error(__('The file could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
