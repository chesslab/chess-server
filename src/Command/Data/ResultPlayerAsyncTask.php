<?php

namespace ChessServer\Command\Data;

use ChessServer\Db;
use Spatie\Async\Task;

class ResultPlayerAsyncTask extends Task
{
    const SQL_LIKE = [

    ];

    const SQL_EQUAL = [
        'White',
        'Black',
        'Result',
    ];

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
        $sql = 'SELECT ECO, COUNT(*) as total FROM games WHERE ';

        $values = [];

        foreach ($this->params as $key => $val) {
            if (in_array($key, self::SQL_LIKE)) {
                $sql .= "$key LIKE :$key AND ";
                $values[] = [
                    'param' => ":$key",
                    'value' => '%'.$val.'%',
                    'type' => \PDO::PARAM_STR,
                ];
            } else if (in_array($key, self::SQL_EQUAL) && $val) {
                $sql .= "$key = :$key AND ";
                $values[] = [
                    'param' => ":$key",
                    'value' => $val,
                    'type' => \PDO::PARAM_STR,
                ];
            }
        }

        str_ends_with($sql, 'WHERE ')
            ? $sql = substr($sql, 0, -6)
            : $sql = substr($sql, 0, -4);

        $sql .= 'GROUP BY ECO ORDER BY total DESC';

        return $this->db->query($sql, $values)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
