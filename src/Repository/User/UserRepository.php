<?php

namespace ChessServer\Repository\User;

use \stdClass;
use Chess\Elo\Game;
use Chess\Elo\Player;
use Chess\Variant\Classical\PGN\AN\Color;
use Chess\Variant\Classical\PGN\AN\Termination;
use Spatie\Async\Pool;

class UserRepository
{
    protected Pool $pool;

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    public function updateElo(string $result, stdClass $decoded): void
    {
        if ($decoded->elo->{Color::W} && $decoded->elo->{Color::B}) {
            $eloRating = $this->eloRating(
                $result,
                $decoded->elo->{Color::W},
                $decoded->elo->{Color::B}
            );

            $env = [
                'db' => [
                    'driver' => $_ENV['DB_DRIVER'],
                    'host' => $_ENV['DB_HOST'],
                    'database' => $_ENV['DB_DATABASE'],
                    'username' => $_ENV['DB_USERNAME'],
                    'password' => $_ENV['DB_PASSWORD'],
                ],
            ];

            $this->pool->add(new UpdateEloAsyncTask([
                'username' => $decoded->username->{Color::W},
                'elo' => $eloRating[Color::W],
            ], $env));

            $this->pool->add(new UpdateEloAsyncTask([
                'username' => $decoded->username->{Color::B},
                'elo' => $eloRating[Color::B],
            ], $env));
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
}
