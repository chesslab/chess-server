<?php

namespace ChessServer\Repository;

use \stdClass;
use Chess\Elo\Game;
use Chess\Elo\Player;
use Chess\Variant\Classical\PGN\AN\Color;
use Chess\Variant\Classical\PGN\AN\Termination;
use ChessServer\Db;

class UserRepository
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function updateElo(string $result, stdClass $decoded): void
    {
        if ($decoded->elo->{Color::W} && $decoded->elo->{Color::B}) {
            $eloRating = $this->eloRating(
                $result,
                $decoded->elo->{Color::W},
                $decoded->elo->{Color::B}
            );
            $this->updateEloQuery($decoded->username->{Color::W}, $eloRating[Color::W]);
            $this->updateEloQuery($decoded->username->{Color::B}, $eloRating[Color::B]);
        }
    }

    protected function eloRating(string $result, int $i, int $j): array
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

    protected function updateEloQuery(string $username, int $elo): void
    {
        $sql = "UPDATE users SET elo = :elo WHERE username = :username";
        $values= [
            [
                'param' => ":username",
                'value' => $username,
                'type' => \PDO::PARAM_STR,
            ],
            [
                'param' => ":elo",
                'value' => $elo,
                'type' => \PDO::PARAM_INT,
            ],
        ];

        $this->db->query($sql, $values);
    }
}
