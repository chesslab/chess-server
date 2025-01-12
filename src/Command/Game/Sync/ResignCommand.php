<?php

namespace ChessServer\Command\Game\Sync;

use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Command\AbstractSyncCommand;
use ChessServer\Command\Game\Sync\UpdateEloTask;
use ChessServer\Socket\AbstractSocket;

class ResignCommand extends AbstractSyncCommand
{
    public function __construct()
    {
        $this->name = '/resign';
        $this->description = 'Resigns a game.';
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
                'color' => $gameMode->getGame()->getResignation(),
            ],
        ]);
    }
}
