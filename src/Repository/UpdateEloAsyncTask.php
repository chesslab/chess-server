<?php

namespace ChessServer\Repository;

use ChessServer\Db;
use Spatie\Async\Task;

class UpdateEloAsyncTask extends Task
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
        $sql = "UPDATE users SET elo = :elo WHERE username = :username";

        $values= [
            [
                'param' => ":username",
                'value' => $this->params['username'],
                'type' => \PDO::PARAM_STR,
            ],
            [
                'param' => ":elo",
                'value' => $this->params['elo'],
                'type' => \PDO::PARAM_INT,
            ],
        ];

        return $this->db->query($sql, $values);
    }
}
