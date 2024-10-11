<?php

namespace ChessServer\Command\Game\Async;

use Chess\Elo\Game;
use Chess\Elo\Player;
use Chess\Variant\Classical\PGN\AN\Color;
use Chess\Variant\Classical\PGN\AN\Termination;
use ChessServer\Command\AbstractDbAsyncTask;

class UpdateEloTask extends AbstractDbAsyncTask
{
    protected function elo(string $result, int $i, int $j): array
    {
        $w = new Player($i);
        $b = new Player($j);
        $g =  (new Game($w, $b))->setK(32);

        if ($result === Termination::WHITE_WINS) {
            $g->setScore(1, 0)->count();
        } elseif ($result === Termination::DRAW) {
            $g->setScore(0, 0)->count();
        } elseif ($result === Termination::BLACK_WINS) {
            $g->setScore(0, 1)->count();
        }

        return [
            Color::W => $w->getRating(),
            Color::B => $b->getRating(),
        ];
    }

    protected function query(array $params)
    {
        $sql = "UPDATE users SET elo = :elo WHERE username = :username";

        $values= [
            [
                'param' => ":username",
                'value' => $params['username'],
                'type' => \PDO::PARAM_STR,
            ],
            [
                'param' => ":elo",
                'value' => $params['elo'],
                'type' => \PDO::PARAM_INT,
            ],
        ];

        return $this->db->query($sql, $values);
    }

    public function run()
    {
        $elo = $this->elo(
            $this->params['result'],
            $this->params['decoded']->elo->{Color::W},
            $this->params['decoded']->elo->{Color::B}
        );

        $this->query([
            'username' => $this->params['decoded']->username->{Color::W},
            'elo' => $elo[Color::W],
        ]);

        $this->query([
            'username' => $this->params['decoded']->username->{Color::B},
            'elo' => $elo[Color::B],
        ]);
    }
}
