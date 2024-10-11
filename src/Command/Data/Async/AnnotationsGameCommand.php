<?php

namespace ChessServer\Command\Data\Async;

use ChessServer\Command\AbstractAsyncCommand;
use ChessServer\Socket\AbstractSocket;

class AnnotationsGameCommand extends AbstractAsyncCommand
{
    public function __construct()
    {
        $this->name = '/annotations_game';
        $this->description = 'Annotated chess games.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $this->pool->add(new AnnotationsGameTask(), 128000)
            ->then(function ($result) use ($socket, $id) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => $result,
                ]);
            });
    }
}
