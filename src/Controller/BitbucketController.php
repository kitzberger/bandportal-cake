<?php

namespace App\Controller;

use Cake\Cache\Cache;

class BitbucketController extends AppController
{
    #[\Override]
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Bitbucket');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->Bitbucket->authorize();
    }

    public function logout()
    {
        Cache::write('Bitbucket_OAuth2', null);
    }
}
