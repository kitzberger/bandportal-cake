<?php

namespace App\Service;

use App\Mailer\AppMailerTrait;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;

/**
 * Sends notification e-mails to users about new log entries.
 *
 * Extracted from the former LogsController::notify() so it can be invoked
 * from the CLI without instantiating a Controller (Cake 5 requires a
 * ServerRequest in the Controller constructor).
 */
class NotificationService
{
    use AppMailerTrait;

    /**
     * Containments used when querying logs for notification.
     *
     * @var array<string>
     */
    private array $contain = [
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

    /**
     * Notify a user about log entries created since their last notification.
     *
     * @param \Cake\Datasource\EntityInterface|array $user User entity.
     * @return int Number of log entries the user was notified about.
     */
    public function notify($user): int
    {
        $logsTable = TableRegistry::getTableLocator()->get('Logs');
        $logs = $logsTable->find()
            ->contain($this->contain)
            ->orderBy(['Logs.created DESC'])
            ->where([
                'Logs.user_id !=' => $user['id'],
                'Logs.created >' => $user['notified'] ?: '2000-01-01',
                'Logs.share_id IS' => null,
            ]);

        $count = $logs->count();
        if ($count > 0) {
            $this->sendMail(
                __('Notification'),
                null,
                $user['email'],
                'notify',
                [
                    'user' => $user,
                    'logs' => $logs,
                ]
            );
        }

        return $count;
    }
}
