<?php

namespace ChessServer\Command\Auth;

use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\AbstractSocket;

class TotpSignUpCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/totp_signup';
        $this->description = 'TOTP sign up URL.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $conf = [
            'driver' => $_ENV['DB_DRIVER'],
            'host' => $_ENV['DB_HOST'],
            'database' => $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
        ];

        $totp = [
            'secret' => $_ENV['TOTP_SECRET'],
        ];

        $this->pool->add(new TotpSignUpAsyncTask($conf, $totp))
            ->then(function ($result) use ($socket, $id) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => $result,
                ]);
            });
    }
}
