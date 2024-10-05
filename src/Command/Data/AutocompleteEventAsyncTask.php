<?php

namespace ChessServer\Command\Data;

use ChessServer\Db;
use Spatie\Async\Task;

class AutocompleteEventAsyncTask extends Task
{
    private array $params;

    private array $env;

    private Db $db;

    public function __construct(array $params, array $env)
    {
        $this->params = $params;
        $this->env = $env;
    }

    public function configure()
    {
        $this->db = new Db($this->env['db']);
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
