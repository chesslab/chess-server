<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractCommand;
use ChessServer\Command\Data\Pdo;
use ChessServer\Socket\ChesslaBlabSocket;

class StatsPlayerCommand extends AbstractCommand
{
    const SQL_LIKE = [

    ];

    const SQL_EQUAL = [
        'White',
        'Black',
        'Result',
    ];

    public function __construct()
    {
        $this->name = '/stats_player';
        $this->description = 'Statistics about opening results by chess player.';
        $this->params = [
            'settings' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(ChesslaBlabSocket $socket, array $argv, int $id)
    {
        $params = json_decode(stripslashes($argv[1]), true);

        $sql = 'SELECT ECO, COUNT(*) as total FROM games WHERE ';

        $values = [];

        foreach ($params as $key => $val) {
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

        $arr = Pdo::getInstance($this->conf()['database'])
            ->query($sql, $values)
            ->fetchAll(\PDO::FETCH_ASSOC);

        return $socket->getClientStorage()->sendToOne($id, $arr);
    }
}
