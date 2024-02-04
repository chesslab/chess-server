<?php

namespace ChessServer\Command;

use ChessServer\Exception\InternalErrorException;
use ChessServer\Socket\ChesslaBlabSocket;

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

    public function run(ChesslaBlabSocket $socket, array $argv, int $resourceId)
    {
        $gameMode = $socket->getGameModeStorage()->getByResourceId($resourceId);

        if (!$gameMode) {
            throw new InternalErrorException();
        }

        return $socket->getClientsStorage()->sendToOne(
            $resourceId,
            $gameMode->res($argv, $this)
        );
    }
}
