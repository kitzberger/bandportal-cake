<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Mailer\Mailer;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    protected array $enabledFeatures = [];

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');

        $this->viewBuilder()->addHelpers([
            'Tanuck/Markdown.Markdown' => [
                'parser' => 'GithubMarkdown',
            ]
        ]);

        $this->enabledFeatures = [
            'remoteCalendar' => (bool)Configure::read('Calendar.url'),
            'githubRepo' => (bool)Configure::read('Github.repository_url'),
        ];
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\EventInterface $event The beforeRender event.
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        $identity = $this->Authentication->getIdentity();
        if ($identity && !$this->isAuthorized($identity->getOriginalData())) {
            $this->Flash->error('Access denied.');
            return $this->redirect('/');
        }
    }

    public function beforeRender(EventInterface $event)
    {
        $identity = $this->Authentication->getIdentity();
        $currentUser = $identity ? $identity->getOriginalData() : null;
        $this->set('currentUser', $currentUser);

        if (isset($currentUser) && $currentUser['is_passive'] === true) {
            $sharesTable = $this->fetchTable('Shares');
            $shares = $sharesTable->find()
                ->where(['Shares.user_id' => $currentUser['id']])
                ->contain(['Dates', 'Songs', 'Ideas', 'Collections', 'Files']);
            $this->set('currentUserShares', $shares);
        }
        $this->set('controller', $this->request->getParam('controller'));
        $this->set('_csrfToken', $this->request->getAttribute('csrfToken'));

        $this->set('remoteCalendarEnabled', $this->enabledFeatures['remoteCalendar']);
        $this->set('githubEnabled', $this->enabledFeatures['githubRepo']);

        $useJson = $this->request->is('ajax') || $this->request->is('json');

        if (!$useJson &&
            !array_key_exists('_serialize', $this->viewBuilder()->getVars()) &&
            in_array($this->response->getType(), ['application/json', 'application/xml'])
        ) {
            $useJson = true;
            $this->set('_serialize', true);
        }

        if ($useJson) {
            $this->viewBuilder()->setClassName('Json');
            $viewVars = $this->viewBuilder()->getVars();
            if (isset($viewVars['_serialize'])) {
                $this->viewBuilder()->setOption('serialize', $viewVars['_serialize']);
            }
        }
    }

    public function isAuthorized($user)
    {
        // Admin can access every action
        if (isset($user['is_admin']) && $user['is_admin']) {
            return true;
        }

        $this->Flash->error(__('Access denied'));

        // Default deny
        return false;
    }

    public function isAuthorizedByShare($user, $type, $id = null)
    {
        if (is_null($id)) {
            $id = (int)$this->request->getParam('pass')[0];
        }

        $sharesTable = $this->fetchTable('Shares');
        if ($sharesTable->sharedWithUser($type, $id, $user['id'])) {
            return true;
        }

        return false;
    }

    protected function sendMail($subject, $message, $to, $template = 'default', $viewVars = [])
    {
        $mailer = new Mailer('default');

        $from = $mailer->getMessage()->getFrom();
        if (empty($from)) {
            throw new \Exception('Missing default from address in config!');
        }

        if ($message !== null) {
            $mailer->getMessage()->setBodyHtml($message);
        } else {
            $mailer->viewBuilder()->setTemplate($template);
            $mailer->setViewVars($viewVars);
        }

        $mailer
            ->setEmailFormat('html')
            ->setTo($to)
            ->setSubject($subject)
            ->deliver();
    }
}
