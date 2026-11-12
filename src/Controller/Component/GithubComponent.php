<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;

class GithubComponent extends Component
{
    /**
     * Other components utilized by GithubComponent
     */
    public array $components = ['Flash'];

    /**
     * Initialize properties.
     *
     * @param array $config The config data.
     */
    public function initialize(array $config): void
    {
        $this->controller = $this->_registry->getController();
        $this->session = $this->controller->getRequest()->getSession();

        $this->_config['REPOSITORY_URL'] = Configure::read('Github.repository_url');
    }

    public function loadResource($url)
    {
        $url = $this->_config['REPOSITORY_URL'] . $url;
        $url = str_replace(' ', '%20', $url);

        $fileContent = file_get_contents($url);

        if ($fileContent) {
            return $fileContent;
        }
    }
}
