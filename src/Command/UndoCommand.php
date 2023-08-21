<?php

namespace ChessServer\Command;

use ChessServer\Socket;
use ChessServer\Exception\InternalErrorException;
use ChessServer\GameMode\PlayMode;
use Ratchet\ConnectionInterface;

class UndoCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/undo';
        $this->description = 'Undoes the last move.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(Socket $socket, array $argv, ConnectionInterface $from)
    {
        $gameMode = $socket->getGameModeStorage()->getByResourceId($from->resourceId);

        if (!$gameMode) {
            throw new InternalErrorException();
        }

        if (is_a($gameMode, PlayMode::class)) {
            return $socket->sendToMany(
                $gameMode->getResourceIds(),
                $gameMode->res($argv, $this)
            );
        }

        return $socket->sendToOne(
            $from->resourceId,
            $gameMode->res($argv, $this)
        );
    }
}
