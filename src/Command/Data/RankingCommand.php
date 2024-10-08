<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\AbstractSocket;

class RankingCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/ranking';
        $this->description = 'Top players by ELO.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $this->pool->add(new RankingAsyncTask())
            ->then(function ($result) use ($socket, $id) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => $result,
                ]);
            });
    }
}
