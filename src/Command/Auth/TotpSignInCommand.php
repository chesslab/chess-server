<?php

namespace ChessServer\Command\Auth;

use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\AbstractSocket;

class TotpSignInCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/totp_signin';
        $this->description = 'TOTP sign in.';
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

        $this->pool->add(new TotpSignInAsyncTask($params))
            ->then(function ($result) use ($socket, $id) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => $result,
                ]);
            });
    }
}
