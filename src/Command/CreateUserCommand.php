<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\BaseCommand;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\TableRegistry;

/**
 * Create new user command.
 */
class CreateUserCommand extends BaseCommand
{
    /**
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io.
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $io->out('Create new user:');
        $io->hr();

        $username = '';
        while ($username === '') {
            $username = $io->ask('Username:');
            if ($username === '') {
                $io->out('Username must not be empty!');
            }
        }

        $email = '';
        while ($email === '') {
            $email = $io->ask('Email:');
            if ($email === '') {
                $io->out('Email must not be empty!');
            }
        }

        $password = '';
        while ($password === '') {
            $password = $io->ask('Password:');
            if ($password === '') {
                $io->out('Password must not be empty!');
            }
        }

        $isAdmin = $io->askChoice('Admin?', ['yes', 'no'], 'no');
        $isActive = $io->askChoice('Active?', ['yes', 'no'], 'yes');
        $isPassive = $io->askChoice('Passive?', ['yes', 'no'], 'no');

        $user = $usersTable->newEntity([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'is_admin' => $isAdmin === 'yes',
            'is_active' => $isActive === 'yes',
            'is_passive' => $isPassive === 'yes',
        ]);

        if ($usersTable->save($user)) {
            $io->success('User creation successfully!');

            return static::CODE_SUCCESS;
        }

        $io->err('User creation failed!');
        $io->err(print_r($user->getErrors(), true));

        return static::CODE_ERROR;
    }

    /**
     * @param \Cake\Console\ConsoleOptionParser $parser The option parser.
     */
    #[\Override]
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->setDescription('Create a new user interactively.');

        return $parser;
    }
}
