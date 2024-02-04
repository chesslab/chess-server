<?php

namespace ChessServer\Command;

use ChessServer\Exception\InternalErrorException;
use ChessServer\Game\PlayMode;
use ChessServer\Socket\ChesslaBlabSocket;

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

    public function run(ChesslaBlabSocket $socket, array $argv, int $resourceId)
    {
        $gameMode = $socket->getGameModeStorage()->getByResourceId($resourceId);

        if (!$gameMode) {
            throw new InternalErrorException();
        }

        if (is_a($gameMode, PlayMode::class)) {
            return $socket->getClientStorage()->sendToMany(
                $gameMode->getResourceIds(),
                $gameMode->res($argv, $this)
            );
        }

        return $socket->getClientStorage()->sendToOne(
            $resourceId,
            $gameMode->res($argv, $this)
        );
    }
}
