<?php

namespace ChessServer\Command;

use ChessServer\Socket\ChesslaBlabSocket;

class HeuristicsCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/heuristics';
        $this->description = "Returns the heuristics of a chess position.";
        $this->params = [
            'fen' => '<string>',
            'variant' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(ChesslaBlabSocket $socket, array $argv, int $id)
    {
        $gameMode = $socket->getGameModeStorage()->getById($id);

        return $socket->getClientStorage()->sendToOne(
            $id,
            $gameMode->res($argv, $this)
        );
    }
}
