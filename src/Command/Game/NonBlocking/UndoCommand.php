<?php

namespace ChessServer\Command\Game\NonBlocking;

use ChessServer\Command\AbstractNonBlockingCommand;
use ChessServer\Socket\AbstractSocket;

class UndoCommand extends AbstractNonBlockingCommand
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

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $gameMode = $socket->getGameModeStorage()->getById($id);

        return $socket->getClientStorage()->send(
            $gameMode->getResourceIds(),
            $gameMode->res($argv, $this)
        );
    }
}
