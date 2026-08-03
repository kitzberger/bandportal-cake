<?php

namespace App\Controller;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    #[\Override]
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->addUnauthenticatedActions(['login', 'logout']);
    }

    #[\Override]
    public function isAuthorized($user)
    {
        if (in_array($this->request->getParam('action'), ['login', 'logout'])) {
            return true;
        }

        return parent::isAuthorized($user);
    }

    // public function beforeFilter(Event $event)
    // {
    //     parent::beforeFilter($event);
    //     // Allow users to register and logout.
    //     // You should not add the "login" action to allow list. Doing so would
    //     // cause problems with normal functioning of AuthComponent.
    //     $this->Auth->allow(['add', 'logout']);
    // }

    public function login()
    {
        $this->viewBuilder()->disableAutoLayout();

        if ($this->request->is('post')) {
            $result = $this->Authentication->getResult();
            if ($result && $result->isValid()) {
                $redirectUrl = urldecode((string) $this->request->getData('redirect')) ?: null;
                return $this->redirect($this->Authentication->getLoginRedirect($redirectUrl) ?? ['controller' => 'Misc', 'action' => 'dashboard']);
            }
            $this->Flash->error(__('Invalid username or password, try again'));
        }

        if ($this->request->getData('redirect')) {
            $redirectUrl = $this->urlencode($this->request->getData('redirect'));
        } elseif ($this->request->getQuery('redirect')) {
            $redirectUrl = $this->urlencode($this->request->getQuery('redirect'));
        } elseif (!str_ends_with((string) $this->request->getEnv('HTTP_REFERER'), 'login')) {
            $redirectUrl = $this->urlencode($this->request->getEnv('HTTP_REFERER'));
        } else {
            $redirectUrl = null;
        }
        $this->set('redirect', $redirectUrl);
    }

    protected function urlencode($url)
    {
        return urlencode(urldecode((string) $url));
    }

    public function logout()
    {
        $this->Authentication->logout();
        return $this->redirect('/');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, contain: ['Comments', 'Dates', 'Files', 'Ideas', 'Collections', 'Songs', 'Votes',
                      'Comments.Songs', 'Comments.Ideas', 'Comments.Dates',
                      'Files.Songs', 'Files.Ideas', 'Files.Dates',
                      'Votes.Ideas', 'Votes.Dates'
        ]);

        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            // no new password entered => don't override!
            if (empty($data['password'])) {
                unset($data['password']);
            }
            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
