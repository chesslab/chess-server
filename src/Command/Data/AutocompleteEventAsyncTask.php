<?php

namespace ChessServer\Command\Data;

use ChessServer\Db;
use Spatie\Async\Task;

class AutocompleteEventAsyncTask extends Task
{
    private array $params;

    private array $conf;

    private Db $db;

    public function __construct(array $params, array $conf)
    {
        $this->params = $params;
        $this->conf = $conf;
    }

    public function configure()
    {
        $this->db = new Db($this->conf);
    }

    public function run()
    {
        $key = key($this->params);

        $values[] = [
            'param' => ":$key",
            'value' => '%'. current($this->params) .'%',
            'type' => \PDO::PARAM_STR,
        ];

        $sql = "SELECT DISTINCT $key FROM games WHERE $key LIKE :$key LIMIT 10";

        return $this->db->query($sql, $values)->fetchAll(\PDO::FETCH_COLUMN);
    }
}
