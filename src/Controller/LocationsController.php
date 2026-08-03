<?php

namespace App\Controller;

/**
 * Locations Controller
 *
 * @property \App\Model\Table\LocationsTable $Locations
 */
class LocationsController extends AppController
{
    #[\Override]
    public function isAuthorized($user)
    {
        $action = $this->request->getParam('action');

        if ($user['is_active']) {
            if (in_array($action, ['index', 'view', 'add', 'edit'])) {
                return true;
            }

            if (in_array($action, ['delete'])) {
                $dateId = (int)$this->request->getParam('pass')[0];
                if ($this->Locations->isOwnedBy($dateId, $user['id'])) {
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

        $query = $this->Locations
            ->find()
            ->contain(['Users'])
            ->order(['Locations.modified DESC'])
            ->where([
                'OR' => [
                    'Locations.title LIKE' => '%' . $sword . '%',
                    'Locations.city LIKE' => '%' . $sword . '%',
                ],
            ]);
        $locations = $this->paginate($query);

        $this->set(compact('locations', 'sword'));
        $this->set('_serialize', ['locations']);
    }

    /**
     * View method
     *
     * @param string|null $id Location id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $location = $this->Locations->get($id, [
            'contain' => ['Users']
        ]);

        $this->set('location', $location);
        $this->set('_serialize', ['location']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $location = $this->Locations->newEmptyEntity();
        if ($this->request->is('post')) {
            $location = $this->Locations->patchEntity($location, $this->request->getData());
            if ($this->Locations->save($location)) {
                $this->Flash->success(__('The location has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The location could not be saved. Please, try again.'));
            }
        }
        $users = $this->Locations->Users->find('list')->limit(200);
        $this->set(compact('location', 'users'));
        $this->set('_serialize', ['location']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Location id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $location = $this->Locations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $location = $this->Locations->patchEntity($location, $this->request->getData());
            if ($this->Locations->save($location)) {
                $this->Flash->success(__('The location has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The location could not be saved. Please, try again.'));
            }
        }
        $users = $this->Locations->Users->find('list')->limit(200);
        $this->set(compact('location', 'users'));
        $this->set('_serialize', ['location']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Location id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $location = $this->Locations->get($id);
        if ($this->Locations->delete($location)) {
            $this->Flash->success(__('The location has been deleted.'));
        } else {
            $this->Flash->error(__('The location could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
