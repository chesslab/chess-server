<?php

namespace ChessServer\Command\Data\Async;

use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\AbstractSocket;

class ResultCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/result';
        $this->description = 'Openings results.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $this->pool->add(new ResultTask(), 81920)
            ->then(function ($result) use ($socket, $id) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => $result,
                ]);
            });
    }
}
