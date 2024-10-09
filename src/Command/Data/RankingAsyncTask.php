<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractDbAsyncTask;

class RankingAsyncTask extends AbstractDbAsyncTask
{
    public function run()
    {
        $sql = "SELECT username, elo FROM users WHERE lastLoginAt IS NOT NULL ORDER BY elo DESC LIMIT 20";

        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
