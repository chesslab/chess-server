<?php

namespace ChessServer\Command\Auth;

use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\AbstractSocket;

class TotpRefreshCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/totp_refresh';
        $this->description = 'Refresh the TOTP access token.';
        $this->params = [
            'params' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $params = json_decode(stripslashes($argv[1]), true);

        $env = [
            'db' => [
                'driver' => $_ENV['DB_DRIVER'],
                'host' => $_ENV['DB_HOST'],
                'database' => $_ENV['DB_DATABASE'],
                'username' => $_ENV['DB_USERNAME'],
                'password' => $_ENV['DB_PASSWORD'],
            ],
            'jwt' => [
                'iss' => $_ENV['JWT_ISS'],
                'secret' => $_ENV['JWT_SECRET'],
            ],
        ];

        $this->pool->add(new TotpRefreshAsyncTask($params, $env))
            ->then(function ($result) use ($socket, $id) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => $result,
                ]);
            });
    }
}
