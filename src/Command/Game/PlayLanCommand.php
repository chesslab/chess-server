<?php

namespace ChessServer\Command\Game;

use ChessServer\Command\AbstractCommand;
use ChessServer\Command\Game\Mode\PlayMode;
use ChessServer\Socket\AbstractSocket;

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

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $gameMode = $socket->getGameModeStorage()->getById($id);

        if (is_a($gameMode, PlayMode::class)) {
            return $socket->getClientStorage()->sendToMany(
                $gameMode->getResourceIds(),
                $gameMode->res($argv, $this)
            );
        }

        return $socket->getClientStorage()->sendToOne(
            $id,
            $gameMode->res($argv, $this)
        );
    }
}
