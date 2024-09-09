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
            'settings' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $settings = json_decode(stripslashes($argv[1]), true);

        $gameMode = $socket->getGameModeStorage()->getById($id);

        if (is_a($gameMode, PlayMode::class)) {
            return $socket->getClientStorage()->sendToMany(
                $gameMode->getResourceIds(),
                $gameMode->res($settings, $this)
            );
        }

        return $socket->getClientStorage()->sendToOne(
            $id,
            $gameMode->res($settings, $this)
        );
    }
}
