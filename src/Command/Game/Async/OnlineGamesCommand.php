<?php

namespace ChessServer\Command\Game\Sync;

use ChessServer\Command\AbstractSyncCommand;
use ChessServer\Command\Game\Mode\PlayMode;
use ChessServer\Socket\AbstractSocket;

class OnlineGamesCommand extends AbstractSyncCommand
{
    public function __construct()
    {
        $this->name = '/online_games';
        $this->description = "Returns the online games in pending status to be accepted.";
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        return $socket->getClientStorage()->send([$id], [
            $this->name => $socket
                ->getGameModeStorage()
                ->decodeByPlayMode(PlayMode::STATUS_PENDING, PlayMode::SUBMODE_ONLINE),
        ]);
    }
}
