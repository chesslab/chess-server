<?php

namespace ChessServer\Command;

use ChessServer\Socket;
use ChessServer\GameMode\PlayMode;
use Ratchet\ConnectionInterface;

class LegalCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/legal';
        $this->description = 'Returns the legal FEN positions of a piece.';
        $this->params = [
            'position' => '<string>',
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
