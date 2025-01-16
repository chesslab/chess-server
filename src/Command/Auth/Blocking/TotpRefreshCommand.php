<?php

namespace ChessServer\Command\Auth\Blocking;

use ChessServer\Command\AbstractBlockingCommand;
use ChessServer\Socket\AbstractSocket;

class TotpRefreshCommand extends AbstractBlockingCommand
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
        $params = $this->params($argv[1]);

        $this->pool->add(new TotpRefreshTask($params))
            ->then(function ($result) use ($socket, $id) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => $result,
                ]);
            });
    }
}
