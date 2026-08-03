<?php

namespace App\Controller;

use App\Helper\CalDAV;
use Cake\I18n\FrozenDate;

/**
 * Dates Controller
 *
 * @property \App\Model\Table\DatesTable $Dates
 */
class DatesController extends AppController
{
    #[\Override]
    public function isAuthorized($user)
    {
        $action = $this->request->getParam('action');

        if ($user['is_active']) {
            if (in_array($action, ['index', 'view', 'add', 'share', 'download', 'edit'])) {
                return true;
            }

            if (in_array($action, ['delete'])) {
                $dateId = (int)$this->request->getParam('pass')[0];
                if ($this->Dates->isOwnedBy($dateId, $user['id'])) {
                    return true;
                }
            }
        } else {
            if (in_array($action, ['view'])) {
                if (parent::isAuthorizedByShare($user, 'date')) {
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

        $now = new FrozenDate();
        $firstOfMonth = clone $now->startOfMonth();
        $lastOfMonth = clone $now->endOfMonth()->modify('+1 day');
        ;
        #debug($firstOfMonth); debug($lastOfMonth);
        $this->paginate = [
            'limit' => 200,
        ];

        $query = $this->Dates
            ->find()
            ->contain(['Users', 'Votes'])
            ->order(['Dates.begin ASC'])
            ->where([
                'OR' => [
                    [
                        'Dates.begin >=' => $firstOfMonth,
                    ],
                    [
                        'Dates.end >=' => $firstOfMonth,
                    ],
                ],
                ['Dates.title LIKE' => '%' . $sword . '%'],
            ]);
        $dates = $this->paginate($query);

        $this->set(compact('dates', 'sword'));
        $this->set('_serialize', ['dates']);
    }

    /**
     * View method
     *
     * @param string|null $id Date id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $usersTable = $this->fetchTable('Users');
        $users = $usersTable->find(
            'all',
            [
                'conditions' => [
                    'Users.is_active' => true,
                ],
            ]
        );
        $passiveUsers = $usersTable->find(
            'all',
            [
                'conditions' => [
                    'Users.is_passive' => true,
                ],
            ]
        );

        $date = $this->Dates->get($id, [
            'contain' => [
                'Users',
                'Locations',
                'Comments' => ['sort' => ['Comments.modified' => 'ASC']],
                'Files',
                'Votes',
                'Comments.Users',
                'Files.Users',
                'Votes.Users',
                'Shares',
                'Shares.Users'
            ]
        ]);

        $thisDaysBegin = $date->begin->startOfDay();
        $thisDaysEnd = $date->end ? $date->end->endOfDay() : $date->begin->endOfDay();

        $collidingDates = $this->Dates->find(
            'all',
            [
                'contain' => ['Users'],
                'conditions' => [
                    [
                        'Dates.id !=' => $id,
                    ],
                    'OR' => [
                        [
                            'Dates.begin >=' => $thisDaysBegin,
                            'Dates.begin <=' => $thisDaysEnd,
                        ],
                        [
                            'Dates.end >=' => $thisDaysBegin,
                            'Dates.end <=' => $thisDaysEnd,
                        ],
                        [
                            'Dates.begin <=' => $thisDaysBegin,
                            'Dates.end >=' => $thisDaysEnd,
                        ],
                    ],
                ],
            ]
        );

        $this->set('users', $users);
        $this->set('passiveUsers', $passiveUsers);
        $this->set('date', $date);
        $this->set('collidingDates', $collidingDates);
        $this->set('_serialize', ['date', 'collidingDates']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $date = $this->Dates->newEmptyEntity();
        if ($this->request->is('post')) {
            $date = $this->Dates->patchEntity($date, $this->request->getData());
            if ($this->Dates->save($date)) {
                if ($this->enabledFeatures['remoteCalendar']) {
                    if ($date->status > 0) {
                        try {
                            $uri = CalDAV::putEvent($date);
                            $date->uri = $uri;
                            $this->Dates->save($date);
                            $this->Flash->success(__('The date has been published on the remote calendar.'));
                        } catch (\Exception) {
                            $this->Flash->error(__('The date could not be put on the remote calendar. Please, try again.'));
                        }
                    }
                }

                if ($this->request->is('ajax')) {
                    if ($this->request->is('json')) {
                    }
                    // no return, so the json rendering kicks in.
                } else {
                    $this->Flash->success(__('The date has been saved.'));
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error(__('The date could not be saved. Please, try again.'));
            }
        }
        $users = $this->Dates->Users->find('list', ['limit' => 200, 'order' => 'username']);
        $locations = $this->Dates->Locations->find('list', ['limit' => 200, 'order' => 'title']);
        $this->set(compact('date', 'users', 'locations'));
        $this->set('_serialize', ['date']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Date id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $date = $this->Dates->get($id, [
            'contain' => ['Locations']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $date = $this->Dates->patchEntity($date, $this->request->getData());
            if ($this->Dates->save($date)) {
                if ($this->enabledFeatures['remoteCalendar']) {
                    if ($date->uri || $date->status > 0) {
                        try {
                            $uri = CalDAV::putEvent($date);
                            $date->uri = $uri;
                            $this->Dates->save($date);
                            $this->Flash->success(__('The date has been updated on the remote calendar.'));
                        } catch (\Exception) {
                            $this->Flash->error(__('The date could not be updated on the remote calendar. Please, try again.'));
                        }
                    }
                }
                $this->Flash->success(__('The date has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The date could not be saved. Please, try again.'));
            }
        }
        $users = $this->Dates->Users->find('list', ['limit' => 200, 'order' => 'username']);
        $locations = $this->Dates->Locations->find('list', ['limit' => 200, 'order' => 'title']);
        $this->set(compact('date', 'users', 'locations'));
        $this->set('_serialize', ['date']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Date id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $date = $this->Dates->get($id);
        if ($this->Dates->delete($date)) {
            if ($this->enabledFeatures['remoteCalendar'] && $date->uri) {
                try {
                    CalDAV::deleteEvent($date);
                    $this->Flash->success(__('The date has been deleted from the remote calendar.'));
                } catch (\Exception) {
                    $this->Flash->error(__('The date could not be deleted from the remote calendar. Please, try again.'));
                }
            }
            $this->Flash->success(__('The date has been deleted.'));
        } else {
            $this->Flash->error(__('The date could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Publishs a date onto the remote calendar
     *
     * @param string|null $id Date id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function publish($id = null)
    {
        $date = $this->Dates->get($id, [
            'contain' => ['Locations']
        ]);

        if ($this->enabledFeatures['remoteCalendar']) {
            $uri = null;
            try {
                $uri = CalDAV::putEvent($date);
            } catch (\Exception $e) {
                $this->Flash->error($e->getMessage());
            }

            if (!empty($uri)) {
                $date->uri = $uri;
                if ($this->Dates->save($date)) {
                    $this->Flash->success(__('The date has been published on the remote calendar.'));
                } else {
                    $this->Flash->error(__('The date could not be published on the remote calendar. Please, try again.'));
                }
            } else {
                $this->Flash->error(__('The date could not be published on the remote calendar. Please, try again.'));
            }
        }

        return $this->redirect(['action' => 'view', $date->id]);
    }

    /**
     * Unpublishs a date on the remote calendar
     *
     * @param string|null $id Date id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function unpublish($id = null)
    {
        $date = $this->Dates->get($id);

        if ($this->enabledFeatures['remoteCalendar']) {
            $success = null;
            try {
                $success = CalDAV::deleteEvent($date);
            } catch (\Exception $e) {
                $this->Flash->error($e->getMessage());
            }

            if (!empty($success)) {
                $date->uri = null;
                if ($this->Dates->save($date)) {
                    $this->Flash->success(__('The date has been unpublished on the remote calendar.'));
                } else {
                    $this->Flash->error(__('The date could not be unpublished on the remote calendar. Please, try again.'));
                }
            } else {
                $this->Flash->error(__('The date could not be unpublished on the remote calendar. Please, try again.'));
            }
        }

        return $this->redirect(['action' => 'view', $date->id]);
    }

    /**
     * Downloads a remote ics file from remote calendar
     *
     * @param string|null $id Date id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function download($id = null)
    {
        $date = $this->Dates->get($id, [
            'contain' => []
        ]);

        if ($this->enabledFeatures['remoteCalendar']) {
            $uri = null;
            try {
                $event = CalDAV::getEvent($date);
                header('Content-Type: text/calendar; charset=utf-8');
                header('Content-Disposition: attachment; filename=' . $event['uri']);
                echo $event['data'];
                exit;
            } catch (\Exception $e) {
                $this->Flash->error($e->getMessage());
            }
        }

        return $this->redirect(['action' => 'view', $date->id]);
    }

    /**
     * Shares a date with a passive user.
     *
     * @param string|null $date Date id.
     * @param string|null $user User id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function share($date = null, $user = null, $template = 'share')
    {
        $usersTable = $this->fetchTable('Users');
        $sharesTable = $this->fetchTable('Shares');

        $date = $this->Dates->get($date, [
            'contain' => ['Locations']
        ]);
        $user = $usersTable->get($user, [
            'conditions' => [
                'Users.is_passive' => true,
            ],
        ]);

        $subject = __('A date is now shared with you!');
        $message = __('What\'s your opinion on this here?');

        if ($user) {
            if (!$sharesTable->sharedWithUser('date', $date->id, $user['id'])) {
                $share = $sharesTable->newEmptyEntity();
                $share->user = $user;
                $share->date = $date;
                $sharesTable->save($share);
            }

            $this->sendMail($subject, $message, $user->email, $template, ['user' => $user, 'date' => $date]);
            $this->Flash->success(__('The user has been informed.'));
        } else {
            $this->Flash->error(__('The user could not be informed. Please, try again.'));
        }

        return $this->redirect(['action' => 'view', $date->id]);
    }
}
