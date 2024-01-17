<?php

namespace ChessServer\Command;

use ChessServer\Socket\ChesslaBlab;
use ChessServer\Exception\InternalErrorException;

class TutorFenCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/tutor_fen';
        $this->description = "Explains a FEN position in terms of chess concepts.";
        $this->params = [
            // mandatory params
            'fen' => '<string>',
            'variant' => '<string>',
            // optional params
            'startPos' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params) || count($this->params) - 1;
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
