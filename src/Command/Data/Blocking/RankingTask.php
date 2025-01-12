<?php

namespace ChessServer\Command\Data\Blocking;

use ChessServer\Command\AbstractDbBlockingTask;

class RankingTask extends AbstractDbBlockingTask
{
    public function run()
    {
        $sql = "SELECT username, elo FROM users WHERE lastLoginAt IS NOT NULL ORDER BY elo DESC LIMIT 20";

        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
