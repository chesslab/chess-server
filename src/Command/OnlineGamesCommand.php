<?php

namespace ChessServer\Command;

use ChessServer\Socket;
use Ratchet\ConnectionInterface;

class OnlineGamesCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/online_games';
        $this->description = "Returns the online games waiting to be accepted.";
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(Socket $socket, array $argv, ConnectionInterface $from)
    {
        return $socket->sendToOne($from->resourceId, [
            $this->name => $socket->getPendingGames(),
        ]);
    }
}
