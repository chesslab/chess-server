<?php

namespace ChessServer\Command;

use ChessServer\Socket\ChessSocket;
use ChessServer\GameMode\PlayMode;
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

    public function run(ChessSocket $socket, array $argv, ConnectionInterface $from)
    {
        return $socket->sendToOne($from->resourceId, [
            $this->name => $socket
                ->getGameModeStorage()
                ->decodeByPlayMode(PlayMode::STATUS_PENDING, PlayMode::SUBMODE_ONLINE),
        ]);
    }
}
