<?php

namespace ChessServer\Command\Game;

use ChessServer\Db;
use ChessServer\Command\AbstractCommand;
use ChessServer\Repository\UserRepository;
use ChessServer\Socket\AbstractSocket;

class ResignCommand extends AbstractCommand
{
    public function __construct(Db $db)
    {
        parent::__construct($db);

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
        (new UserRepository($this->db))->updateElo(
            $gameMode->getGame()->state()->end['result'],
            $gameMode->getJwtDecoded()
        );

        return $socket->getClientStorage()->send($gameMode->getResourceIds(), [
            $this->name => [
                ...(array) $gameMode->getGame()->state(),
                'color' => $gameMode->getGame()->getResignation(),
            ],
        ]);
    }
}
