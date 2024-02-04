<?php

namespace ChessServer\Command;

use ChessServer\Exception\InternalErrorException;
use ChessServer\Socket\ChesslaBlabSocket;

class StockfishEvalCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/stockfish_eval';
        $this->description = "Returns Stockfish's evaluation for the given position.";
        $this->params = [
            // mandatory params
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

        if (!$gameMode) {
            throw new InternalErrorException();
        }

        return $socket->getClientStorage()->sendToOne(
            $id,
            $gameMode->res($argv, $this)
        );
    }
}
