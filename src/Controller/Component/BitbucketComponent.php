<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Routing\Router;
use Cake\Core\Configure;
use Cake\Cache\Cache;

class BitbucketComponent extends Component
{
    protected $_defaultConfig = [
        'AUTHORIZATION_ENDPOINT' => 'https://bitbucket.org/site/oauth2/authorize',
        'TOKEN_ENDPOINT'         => 'https://bitbucket.org/site/oauth2/access_token',
    ];

    /**
     * Other components utilized by BitbucketComponent
     *
     * @var array
     */
    public $components = ['RequestHandler', 'Flash'];

    /**
     * Instance of the Session object
     *
     * @var \Cake\Network\Session
     * @deprecated 3.1.0 Will be removed in 4.0
     */
    public $session;


    /**
     * Initialize properties.
     *
     * @param array $config The config data.
     */
    public function initialize(array $config): void
    {
        $this->controller = $this->_registry->getController();
        $this->session = $this->controller->request->session();

        $this->_config['CLIENT_ID']      = Configure::read('Bitbucket.client_id');
        $this->_config['CLIENT_SECRET']  = Configure::read('Bitbucket.client_secret');
        $this->_config['REPOSITORY_URL'] = Configure::read('Bitbucket.repository_url');

        // absolute URL to oauth controller
        $authUrl  = 'http' . ($this->controller->request->env('SERVER_PORT')==443 ? 's' : '') . '://';
        $authUrl .= $this->controller->request->env('HTTP_HOST');
        $authUrl .= Router::url(['controller' => 'Bitbucket', 'action' => 'index']);
        $this->_config['REDIRECT_URI'] = $authUrl;
    }

    /**
     * @return \Cake\Network\Response|null
     */
    public function authorize()
    {
        $oauth2 = Cache::read('Bitbucket_OAuth2');

        if ($oauth2) {
            #debug($oauth2);
            $this->Flash->set('Already authorized!');
        }

        $client = new \OAuth2\Client($this->_config['CLIENT_ID'], $this->_config['CLIENT_SECRET']);

        // not authorized
        if (empty($oauth2)) {
            // Step 1: generate bitbucket authentication url
            if (!isset($_GET['code'])) {
                $auth_url = $client->getAuthenticationUrl($this->_config['AUTHORIZATION_ENDPOINT'], $this->_config['REDIRECT_URI']);
                // debug($auth_url); exit;
                header('Location: ' . $auth_url);
                exit;
            }
            // Step 2: come back from bitbucket with "code" parameter
            else {
                $params = ['code' => $_GET['code'], 'redirect_uri' => $this->_config['REDIRECT_URI']];

                // Step 3: get bitbucket access_token
                $response = $client->getAccessToken($this->_config['TOKEN_ENDPOINT'], \OAuth2\Client::GRANT_TYPE_AUTH_CODE, $params);

                if (is_string($response['result'])) {
                    parse_str($response['result'], $OAuth2);
                } else {
                    $OAuth2 = $response['result'];
                }

                Cache::write('Bitbucket_OAuth2', $OAuth2);

                $this->Flash->set('Now connected to bitbucket.org!');

                if ($url = $this->session->read('Bitbucket_OAuth2_redirect')) {
                    $this->session->write('Bitbucket_OAuth2_redirect', null);
                    $this->controller->redirect($url);
                }
            }
        }
        if (empty(Cache::read('Bitbucket_OAuth2'))) {
            $this->Flash->set(__('Currently not authenticated to bitbucket.org!'));
        }
    }

    public function loadResource($url)
    {
        $url = $this->_config['REPOSITORY_URL'] . $url;
        $oauth2 = Cache::read('Bitbucket_OAuth2');

        if (!empty($oauth2)) {
            $client = new \OAuth2\Client($this->_config['CLIENT_ID'], $this->_config['CLIENT_SECRET']);
            $client->setAccessToken($oauth2['access_token']);
            $response = $client->fetch($url);
            if ($response['code'] == 200) {
                return $response['result'];
            } else {
                return print_r($response, true);
            }
        }
        $this->Flash->set('Not authorized to bitbucket.org');
        $this->session->write('Bitbucket_OAuth2_redirect', '/' . $this->controller->request->url);
        $this->controller->redirect($this->_config['REDIRECT_URI']);
    }
}
