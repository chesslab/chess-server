<?php

namespace ChessServer\Command;

use ChessServer\Game\PlayMode;
use ChessServer\Socket\ChesslaBlabSocket;

class ResignCommand extends AbstractCommand
{
    const ACTION_ACCEPT    = 'accept';

    public function __construct()
    {
        $this->name = '/resign';
        $this->description = 'Allows to resign a game.';
        $this->params = [
            // mandatory param
            'action' => [
                self::ACTION_ACCEPT,
            ],
        ];
    }

    public function validate(array $argv)
    {
        if (in_array($argv[1], $this->params['action'])) {
            return count($argv) - 1 === count($this->params);
        }

        return false;
    }

    public function run(ChesslaBlabSocket $socket, array $argv, int $id)
    {
        $gameMode = $socket->getGameModeStorage()->getById($id);

        if (is_a($gameMode, PlayMode::class)) {
            return $socket->getClientStorage()->sendToMany($gameMode->getResourceIds(), [
                $this->name => [
                    'action' => $argv[1],
                ],
            ]);
        }
    }
}
