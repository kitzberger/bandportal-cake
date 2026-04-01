<?php

namespace App\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use App\Controller\LogsController;

/**
 * Notification shell command.
 */
class NotificationShell extends Shell
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Logs');
        $this->loadModel('Users');
    }

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();
        $parser->addArgument(
            'specificUser',
            [
                'help' => 'Send notifications to a specify user only? This won\' set to notified timestamp for that user.',
            ]
        );
        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int Success or error code.
     */
    public function main($specificUser = null)
    {
        $options = [
            'conditions' => [
                'Users.is_active' => true,
            ],
        ];
        if (!empty($specificUser)) {
            if (is_numeric($specificUser)) {
                $options['conditions'][] = ['Users.id' => (int)$specificUser];
            } else {
                $options['conditions'][] = ['Users.username' => (string)$specificUser];
            }
        }
        $users = $this->Users->find('all', $options);

        if ($users->count() === 0) {
            $this->out('No active user(s) found ;-(');
        } else {
            foreach ($users as $user) {
                $this->out('Informing ' . $user['username'] . ' about latest stuff ...');

                $logsController = new LogsController();
                $result = $logsController->notify($user);

                $this->out('-> ' . $result . ' log(s) sent');

                if ($result > 0 && empty($specificUser)) {
                    $user->notified = \Cake\I18n\FrozenTime::now();
                    $this->Users->save($user);
                    $this->out('-> Set "notified" to: ' . $user->notified);
                }
            }
        }
    }
}
