<?php

namespace ChessServer\Command\Data;

use Chess\Elo\Game;
use Chess\Elo\Player;
use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Socket\AbstractSocket;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class EloCommand extends AbstractDataCommand
{
    public function __construct(Db $db)
    {
        parent::__construct($db);

        $this->name = '/elo';
        $this->description = 'Updates the ELO of the players after a game has been played.';
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

        /*
        $decoded = JWT::decode($params['accessToken'], new Key($_ENV['JWT_SECRET'], 'HS256'));

        $w = new Player($decoded['elo'][Color::W]);
        $b = new Player($decoded['elo'][Color::B]);

        $game =  new Game($w, $b);
        $game->setK(32)
            ->setScore(1, 0)
            ->count();
        */

        // $this->assertEquals(1516, $w->getRating());
        // $this->assertEquals(1484, $b->getRating());

        // TODO ...

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => $params,
        ]);
    }
}
