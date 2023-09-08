<?php

namespace ChessServer\Command;

use ChessServer\Socket\ChesslaBlab;
use ChessServer\Game\PlayMode;

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

    public function run(ChesslaBlab $socket, array $argv, int $resourceId)
    {
        return $socket->sendToOne($resourceId, [
            $this->name => $socket
                ->getGameModeStorage()
                ->decodeByPlayMode(PlayMode::STATUS_PENDING, PlayMode::SUBMODE_ONLINE),
        ]);
    }
}
