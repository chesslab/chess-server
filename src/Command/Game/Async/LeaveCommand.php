<?php

namespace ChessServer\Command\Game\Async;

use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Command\AbstractAsyncCommand;
use ChessServer\Command\Game\Async\UpdateEloTask;
use ChessServer\Socket\AbstractSocket;

class LeaveCommand extends AbstractAsyncCommand
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
        if ($gameMode = $socket->getGameModeStorage()->getById($id)) {
            $params = json_decode(stripslashes($argv[1]), true);

            $gameMode->getGame()->setResignation($params['color']);

            if ($gameMode->getJwtDecoded()->elo->{Color::W} &&
                $gameMode->getJwtDecoded()->elo->{Color::B}
            ) {
                $this->pool->add(new UpdateEloTask([
                    'result' => $gameMode->getGame()->state()->end['result'],
                    'decoded' => $gameMode->getJwtDecoded(),
                ]));
            }

            return $socket->getClientStorage()->send($gameMode->getResourceIds(), [
                $this->name => [
                    ...(array) $gameMode->getGame()->state(),
                    'color' => $gameMode->getGame()->getAbandoned(),
                ],
            ]);
        }
    }
}
