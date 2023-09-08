<?php

namespace ChessServer\Command;

use ChessServer\Socket\ChesslaBlab;
use ChessServer\Exception\InternalErrorException;

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

    public function run(ChesslaBlab $socket, array $argv, int $resourceId)
    {
        $gameMode = $socket->getGameModeStorage()->getByResourceId($resourceId);

        if (!$gameMode) {
            throw new InternalErrorException();
        }

        return $socket->sendToOne(
            $resourceId,
            $gameMode->res($argv, $this)
        );
    }
}
