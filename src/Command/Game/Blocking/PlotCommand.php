<?php

namespace ChessServer\Command\Game\Blocking;

use ChessServer\Command\AbstractBlockingCommand;
use ChessServer\Socket\AbstractSocket;

class PlotCommand extends AbstractBlockingCommand
{
    public function __construct()
    {
        $this->name = '/plot';
        $this->description = 'Plots the oscillations of an evaluation feature in the time domain.';
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

        $this->pool->add(new PlotTask($params))
            ->then(function ($result) use ($socket, $id) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => $result,
                ]);
            });
    }
}
