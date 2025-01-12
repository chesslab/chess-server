<?php

namespace ChessServer\Command\Game\Sync;

use ChessServer\Command\AbstractSyncCommand;
use ChessServer\Socket\AbstractSocket;

class AsciiCommand extends AbstractSyncCommand
{
    public function __construct()
    {
        $this->name = '/ascii';
        $this->description = 'Returns an ASCII representation of the board.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $gameMode = $socket->getGameModeStorage()->getById($id);

        return $socket->getClientStorage()->send([$id], [
            $this->name => $gameMode->getGame()->getBoard()->toString(),
        ]);
    }
}
