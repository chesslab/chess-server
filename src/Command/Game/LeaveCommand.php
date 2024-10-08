<?php

namespace ChessServer\Command\Game;

use ChessServer\Command\AbstractCommand;
use ChessServer\Repository\User\UserRepository;
use ChessServer\Socket\AbstractSocket;

class LeaveCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/leave';
        $this->description = 'Leaves a game.';
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

        if ($gameMode = $socket->getGameModeStorage()->getById($id)) {
            $gameMode->getGame()->setAbandoned($params['color']);
            (new UserRepository($this->pool))->updateElo(
                $gameMode->getGame()->state()->end['result'],
                $gameMode->getJwtDecoded()
            );
            return $socket->getClientStorage()->send($gameMode->getResourceIds(), [
                $this->name => [
                    ...(array) $gameMode->getGame()->state(),
                    'color' => $gameMode->getGame()->getAbandoned(),
                ],
            ]);
        }
    }
}
