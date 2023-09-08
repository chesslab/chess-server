<?php

namespace ChessServer\Command;

use ChessServer\Socket\ChesslaBlab;
use ChessServer\Exception\InternalErrorException;
use ChessServer\Game\PlayMode;

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

    public function run(ChesslaBlab $socket, array $argv, int $resourceId)
    {
        $gameMode = $socket->getGameModeStorage()->getByResourceId($resourceId);

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
            $resourceId,
            $gameMode->res($argv, $this)
        );
    }
}
