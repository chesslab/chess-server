<?php

namespace AbstractSyncCommandChessServer\Command\Game\Sync;

use ChessServer\Command\AbstractSyncCommand;
use ChessServer\Socket\AbstractSocket;

class ExtractCommand extends AbstractSyncCommand
{
    public function __construct()
    {
        $this->name = '/extract';
        $this->description = 'Extracts oscillations data from a game.';
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

        $this->pool->add(new ExtractTask($params))
            ->then(function ($result) use ($socket, $id) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => $result,
                ]);
            });
    }
}
