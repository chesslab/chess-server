<?php

namespace ChessServer\Command\Game;

use ChessServer\Command\AbstractCommand;
use ChessServer\Command\Game\Mode\PlayMode;
use ChessServer\Socket\AbstractSocket;

class LeaveCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/leave';
        $this->description = 'Leave a game.';
        $this->params = [
            'params' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $params = json_decode(stripslashes($argv[1]), true);

        $gameMode = $socket->getGameModeStorage()->getById($id);

        if (is_a($gameMode, PlayMode::class)) {
            $gameMode->getGame()->setAbandoned($params['color']);
            return $socket->getClientStorage()->sendToMany($gameMode->getResourceIds(), [
                $this->name => [
                    ...(array) $gameMode->getGame()->state(),
                    'color' => $gameMode->getGame()->getAbandoned(),
                ],
            ]);
        }
    }
}
