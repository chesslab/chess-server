<?php

namespace ChessServer\Command;

use ChessServer\Socket;
use ChessServer\GameMode\PlayMode;
use Ratchet\ConnectionInterface;

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

    public function run(Socket $socket, array $argv, ConnectionInterface $from)
    {
        $gameMode = $socket->getGameMode($from->resourceId);

        if (is_a($gameMode, PlayMode::class)) {
            return $socket->sendToMany(
                $gameMode->getResourceIds(),
                $gameMode->res($argv, $this)
            );
        }

        return $socket->sendToOne(
            $from->resourceId,
            $gameMode->res($argv, $this)
        );
    }
}
