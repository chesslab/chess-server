<?php

namespace ChessServer\Command\Data;

class RankingAsyncTask extends AbstractDataAsyncTask
{
    public function run()
    {
        $sql = "SELECT username, elo FROM users WHERE lastLoginAt IS NOT NULL ORDER BY elo DESC LIMIT 20";

        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
