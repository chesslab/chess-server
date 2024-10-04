<?php

namespace ChessServer\Command\Data;

use ChessServer\Db;
use Spatie\Async\Task;

class RankingAsyncTask extends Task
{
    private array $conf;

    private Db $db;

    public function __construct(array $conf)
    {
        $this->conf = $conf;
    }

    public function configure()
    {
        $this->db = new Db($this->conf);
    }

    public function run()
    {
        $sql = "SELECT username, elo FROM users WHERE lastLoginAt IS NOT NULL ORDER BY elo DESC LIMIT 20";

        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
