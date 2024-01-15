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
            'variant' => '<string>',
            'fen' => '<string>',
            'startPos' => '<string>',
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
