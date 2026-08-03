<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\NotificationService;
use Cake\Console\Arguments;
use Cake\Console\BaseCommand;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;

/**
 * Notification command.
 */
class NotificationCommand extends BaseCommand
{
    /**
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io.
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $query = $usersTable->find()->where(['Users.is_active' => true]);

        $specificUser = $args->getArgument('specificUser');
        if ($specificUser !== null && $specificUser !== '') {
            if (is_numeric($specificUser)) {
                $query->where(['Users.id' => (int)$specificUser]);
            } else {
                $query->where(['Users.username' => $specificUser]);
            }
        }

        if ($query->count() === 0) {
            $io->out('No active user(s) found ;-(');

            return static::CODE_SUCCESS;
        }

        foreach ($query as $user) {
            $io->out('Informing ' . $user['username'] . ' about latest stuff ...');

            $service = new NotificationService();
            $result = $service->notify($user);

            $io->out('-> ' . $result . ' log(s) sent');

            if ($result > 0 && ($specificUser === null || $specificUser === '')) {
                $user->notified = FrozenTime::now();
                $usersTable->save($user);
                $io->out('-> Set "notified" to: ' . $user->notified);
            }
        }

        return static::CODE_SUCCESS;
    }

    /**
     * @param \Cake\Console\ConsoleOptionParser $parser The option parser.
     */
    #[\Override]
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->setDescription('Send notification e-mails to active users about new log entries.')
            ->addArgument('specificUser', [
                'help' => 'Send notifications to a specific user only? This won\'t set the notified timestamp for that user.',
                'required' => false,
            ]);

        return $parser;
    }
}
