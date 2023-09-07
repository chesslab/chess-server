<?php

namespace ChessServer\Command;

use ChessServer\Socket\ChessSocket;
use ChessServer\Exception\InternalErrorException;
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

    public function run(ChessSocket $socket, array $argv, ConnectionInterface $from)
    {
        $gameMode = $socket->getGameModeStorage()->getByResourceId($from->resourceId);

        if (!$gameMode) {
            throw new InternalErrorException();
        }

        return $socket->sendToOne(
            $from->resourceId,
            $gameMode->res($argv, $this)
        );
    }
}
