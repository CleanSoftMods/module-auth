<?php namespace Cms\Modules\Auth\Console;

use Symfony\Component\Console\Input\InputArgument;
use Cms\Modules\Core\Console\BaseCommand;

class MakeUserCommand extends BaseCommand
{
    protected $name = 'make:user';
    protected $readableName = 'User Generator';
    protected $description = 'Create a new CMS user';

    public function fire()
    {
        $userInfo = [
            'username' => $this->argument('username'),
            'email'    => $this->argument('email'),
            'password' => $this->argument('password'),
        ];

        // we are missing some information, try and get it
        if (in_array(null, $userInfo) === true) {
            $this->info('You are missing some information to create this user.');

            if (!array_get($userInfo, 'username', false)) {
                $userInfo['username'] = $this->ask('Username? ');
            }

            if (!array_get($userInfo, 'email', false)) {
                $userInfo['email'] = $this->ask('Email? ');
            }

            if (!array_get($userInfo, 'password', false)) {
                $userInfo['password'] = $this->secret('Password? ');
            }
        }

        if (in_array(null, $userInfo) === true) {
            $this->info('The information required could not be gathered. Please try again.');
            return;
        }

        $event = event('auth.user.register', array($userInfo));

        if ($event[0] instanceof \Cysha\Modules\Auth\Models\User) {
            $this->info('User registered successfully');
            return;
        }

        $this->warn('User was not registered');
    }

    protected function getArguments()
    {
        return array(
            array('username',   InputArgument::OPTIONAL, 'Username to register this user with.'),
            array('email',      InputArgument::OPTIONAL, 'Users email address.'),
            array('password',   InputArgument::OPTIONAL, 'Password to set.'),
        );
    }

}