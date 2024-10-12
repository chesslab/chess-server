<?php

namespace ChessServer\Command\Game\Async;

use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Command\AbstractDbAsyncTask;

class RestartTask extends AbstractDbAsyncTask
{
    public function run()
    {
        $sql = "SELECT * FROM users WHERE username = :username";

        $values[] = [
            'param' => ":username",
            'value' => $this->params['decoded']->username->{Color::W},
            'type' => \PDO::PARAM_STR,
        ];

        $w = $this->db->query($sql, $values)->fetch(\PDO::FETCH_ASSOC);

        $values[] = [
            'param' => ":username",
            'value' => $this->params['decoded']->username->{Color::B},
            'type' => \PDO::PARAM_STR,
        ];

        $b = $this->db->query($sql, $values)->fetch(\PDO::FETCH_ASSOC);

        $decoded = $this->params['decoded'];
        $decoded->iat = time();
        $decoded->exp = time() + 3600; // one hour by default
        $decoded->elo->{Color::W} = $w['elo'] ?? $decoded->elo->{Color::W};
        $decoded->elo->{Color::B} = $b['elo'] ?? $decoded->elo->{Color::B};

        return $decoded;
    }
}
