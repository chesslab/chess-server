<?php

namespace ChessServer\Command;

use ChessServer\Socket;
use ChessServer\Exception\InternalErrorException;
use Ratchet\ConnectionInterface;

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

    public function run(Socket $socket, array $argv, ConnectionInterface $from)
    {
        $gameMode = $socket->getGameModeStorage()->getByResourceId($from->resourceId);

        if (!$gameMode) {
            throw new InternalErrorException();
        }

        return $socket->sendToOne(
            $from->resourceId,
            $gameMode->res($argv, $this)
        );
    }
}
