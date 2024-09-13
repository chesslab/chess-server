<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractCommand;
use ChessServer\Command\Db;
use ChessServer\Socket\AbstractSocket;

class ResultEventCommand extends AbstractCommand
{
    const SQL_LIKE = [

    ];

    const SQL_EQUAL = [
        'Event',
        'Result',
    ];

    public function __construct(Db $db)
    {
        parent::__construct($db);

        $this->name = '/result_event';
        $this->description = 'Openings results by event.';
        $this->params = [
            'params' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
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

        $arr = $this->db->query($sql, $values)->fetchAll(\PDO::FETCH_ASSOC);

        return $socket->getClientStorage()->send([$id], [
            $this->name => $arr,
        ]);
    }
}
