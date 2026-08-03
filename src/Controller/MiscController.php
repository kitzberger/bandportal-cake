<?php

namespace App\Controller;

use App\Model\Entity\Date;
use Cake\I18n\FrozenDate;

/**
 * Misc Controller
 */
class MiscController extends AppController
{
    #[\Override]
    public function isAuthorized($user)
    {
        $action = $this->request->getParam('action');

        if ($user['is_active']) {
            if (in_array($action, ['dashboard'])) {
                return true;
            }
        } else {
            $this->redirect(['controller' => 'pages', 'action' => 'welcome']);
        }

        return parent::isAuthorized($user);
    }

    /**
     * Dashboard method
     *
     * @return \Cake\Http\Response|null
     */
    public function dashboard()
    {
        $datesTable = $this->fetchTable('Dates');
        $songsTable = $this->fetchTable('Songs');
        $ideasTable = $this->fetchTable('Ideas');
        $collectionsTable = $this->fetchTable('Collections');

        $now = new FrozenDate();
        $dates = $datesTable->find()
            ->where([
                'Dates.begin >=' => $now,
                'OR' => [
                    ['Dates.status' => Date::STATUS_UNCONFIRMED],
                    ['Dates.status' => Date::STATUS_CONFIRMED],
                ],
            ])
            ->order('Dates.begin ASC')
            ->limit(5);
        $songs = $songsTable->find()
            ->order('modified DESC')
            ->limit(5);
        $ideas = $ideasTable->find()
            ->contain(['Comments'])
            ->order('modified DESC')
            ->limit(5);
        $collections = $collectionsTable->find()
            ->contain(['Files', 'Songs'])
            ->order('modified DESC')
            ->limit(5);

        $this->set(compact('dates', 'songs', 'ideas', 'collections'));
        $this->set('_serialize', ['dates', 'songs', 'ideas', 'collections']);
    }
}
