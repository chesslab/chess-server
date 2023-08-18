<?php

namespace ChessServer\Command;

use ChessServer\Socket;
use Ratchet\ConnectionInterface;

class StockfishEvalCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/stockfish_eval';
        $this->description = "Returns Stockfish's evaluation for the given position.";
        $this->params = [
            'fen' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(Socket $socket, array $argv, ConnectionInterface $from)
    {
        $gameMode = $socket->getGameModeStorage()->getByResourceId($from->resourceId);

        return $socket->sendToOne(
            $from->resourceId,
            $gameMode->res($argv, $this)
        );
    }
}
