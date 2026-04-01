<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Mailer\Email;

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

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        $this->loadComponent('Auth', [
            'authorize' => ['Controller'],
            'loginRedirect' => [
                'controller' => 'Misc',
                'action' => 'dashboard'
            ],
            'logoutRedirect' => [
                'controller' => 'Pages',
                'action' => 'display',
                'home'
            ]
        ]);

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
    public function beforeRender(EventInterface $event)
    {
        $currentUser = $this->Auth->User();
        $this->set('currentUser', $currentUser);

        if (isset($currentUser) && $currentUser['is_passive'] === true) {
            $this->loadModel('Shares');
            $shares = $this->Shares->find('all', [
                'conditions' => ['Shares.user_id' => $currentUser['id']],
                'contain' => ['Dates', 'Songs', 'Ideas', 'Collections', 'Files']
            ]);
            $this->set('currentUserShares', $shares);
        }
        $this->set('controller', $this->request->getParam('controller'));
        $this->set('_csrfToken', $this->request->getAttribute('csrfToken'));

        $this->set('remoteCalendarEnabled', $this->enabledFeatures['remoteCalendar']);
        $this->set('githubEnabled', $this->enabledFeatures['githubRepo']);

        if (!array_key_exists('_serialize', $this->viewBuilder()->getVars()) &&
            in_array($this->response->getType(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
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

        $this->loadModel('Shares');
        if ($this->Shares->sharedWithUser($type, $id, $user['id'])) {
            return true;
        }

        return false;
    }

    protected function sendMail($subject, $message, $to, $template = 'default', $viewVars = [])
    {
        $deliveryProfile = 'default';
        $config = Email::getConfig($deliveryProfile);
        if (empty($config) || empty($config['from'])) {
            throw new \Exception('Missing default from address in config!');
        }
        $from = $config['from'];

        $email = new Email(['template' => $template, 'layout' => 'default']);
        $email->setTransport('default')
            ->setEmailFormat('html')
            ->setViewVars($viewVars)
            ->setTo($to)
            ->setFrom($from)
            ->setSubject($subject)
            ->send($message);
    }
}
