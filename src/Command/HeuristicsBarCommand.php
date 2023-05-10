<?php

namespace ChessServer\Command;

use ChessServer\Socket;
use ChessServer\GameMode\PlayMode;
use Ratchet\ConnectionInterface;

class HeuristicsBarCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/heuristics_bar';
        $this->description = "Takes an expanded heuristic picture of the current position.";
        $this->params = [
            'fen' => '<string>',
            'variant' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(Socket $socket, array $argv, ConnectionInterface $from)
    {
        $gameMode = $socket->getGameMode($from->resourceId);

        return $socket->sendToOne(
            $from->resourceId,
            $gameMode->res($argv, $this)
        );
    }
}
