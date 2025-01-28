<?php

namespace ChessServer\Command\Game\Blocking;

use ChessServer\Command\AbstractBlockingCommand;
use ChessServer\Socket\AbstractSocket;

class ExtractCommand extends AbstractBlockingCommand
{
    public function __construct()
    {
        $this->name = '/extract';
        $this->description = 'Extracts knowledge from a game by using the Steinitz evaluation.';
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

        $this->pool->add(new ExtractTask($params))
            ->then(function ($result) use ($socket, $id) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => $result,
                ]);
            });
    }
}
