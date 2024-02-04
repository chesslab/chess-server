<?php

namespace ChessServer\Command;

use ChessServer\Exception\InternalErrorException;
use ChessServer\Game\PlayMode;
use ChessServer\Socket\ChesslaBlabSocket;

class PlayLanCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/play_lan';
        $this->description = 'Plays a chess move in long algebraic notation.';
        $this->params = [
            'color' => '<string>',
            'lan' => '<string>',
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

        if (is_a($gameMode, PlayMode::class)) {
            return $socket->getClientsStorage()->sendToMany(
                $gameMode->getResourceIds(),
                $gameMode->res($argv, $this)
            );
        }

        return $socket->getClientsStorage()->sendToOne(
            $resourceId,
            $gameMode->res($argv, $this)
        );
    }
}
