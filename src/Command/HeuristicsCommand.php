<?php

namespace ChessServer\Command;

use ChessServer\Socket;
use ChessServer\GameMode\PlayMode;
use Ratchet\ConnectionInterface;

class HeuristicsCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/heuristics';
        $this->description = "Takes a balanced heuristic picture of the given PGN movetext.";
        $this->params = [
            'movetext' => '<string>',
        ];
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(Socket $socket, array $argv, ConnectionInterface $from)
    {
        $gameMode = $socket->gameModeByResourceId($from->resourceId);

        return $socket->sendToOne(
            $from->resourceId,
            $gameMode->res($argv, $this)
        );
    }
}
