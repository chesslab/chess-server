<?php

namespace ChessServer\Command\Data\Async;

use ChessServer\Command\AbstractAsyncCommand;
use ChessServer\Socket\AbstractSocket;

class ResultEventCommand extends AbstractAsyncCommand
{
    public function __construct()
    {
        $this->name = '/result_event';
        $this->description = 'Openings results by event.';
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

        $this->pool->add(new ResultEventTask($params))
            ->then(function ($result) use ($socket, $id) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => $result,
                ]);
            });
    }
}
