<?php

namespace App\Shell;

use Cake\Console\Shell;

/**
 * Create new user shell command.
 */
class CreateUserShell extends Shell
{
    public function initialize(): void
    {
        parent::initialize();
    }

    public function main()
    {
        $usersTable = $this->fetchTable('Users');
        $this->out('Create new user:');
        $this->hr();

        while (empty($username)) {
            $username = $this->in('Username:');
            if (empty($username)) {
                $this->out('Username must not be empty!');
            }
        }

        while (empty($email)) {
            $email = $this->in('Email:');
            if (empty($email)) {
                $this->out('Email must not be empty!');
            }
        }

        while (empty($password)) {
            $password = $this->in('Password:');
            if (empty($password)) {
                $this->out('Password must not be empty!');
            }
        }

        while (empty($isAdmin)) {
            $isAdmin = $this->in('Admin?', ['yes', 'no'], 'no');
        }

        while (empty($isActive)) {
            $isActive = $this->in('Active?', ['yes', 'no'], 'yes');
        }

        while (empty($isPassive)) {
            $isPassive = $this->in('Passive?', ['yes', 'no'], 'no');
        }

        $user = $usersTable->newEntity([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'is_admin' => $isAdmin=='yes',
            'is_active' => $isActive=='yes',
            'is_passive' => $isPassive=='yes',
        ]);

        if ($usersTable->save($user)) {
            $this->success('User creation successfully!');
        } else {
            $this->err('User creation failed!');
            debug($user->getErrors());
        }
    }
}
