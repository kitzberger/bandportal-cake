<?php

namespace App\Controller;

/**
 * Logs Controller
 *
 * @property \App\Model\Table\LogsTable $Logs
 */
class LogsController extends AppController
{
    protected $contain = [
        'Users',
        'Songs',
        'SongsVersions',
        'SongsVersions.Songs',
        'Dates',
        'Ideas',
        'Comments',
        'Comments.Ideas',
        'Comments.Dates',
        'Comments.Songs',
        'Collections',
        'Votes',
        'Votes.Ideas',
        'Votes.Dates',
        'Files',
        'Files.Ideas',
        'Files.Dates',
        'Files.Songs',
        'Shares',
    ];

    #[\Override]
    public function isAuthorized($user)
    {
        if ($user['is_active']) {
            if (in_array($this->request->getParam('action'), ['index', 'view'])) {
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
        $query = $this->Logs
            ->find()
            ->contain($this->contain);
        $logs = $this->paginate($query, ['order' => ['Logs.created' => 'DESC']]);

        $this->set(compact('logs'));
        $this->set('_serialize', ['logs']);
    }

    /**
     * View method
     *
     * @param string|null $id Log id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $log = $this->Logs->get($id, contain: $this->contain);

        $this->set('log', $log);
        $this->set('_serialize', ['log']);
    }
}
