<?php

namespace ChessServer\Command\Data;

use ChessServer\Data\Pdo;
use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\ChesslaBlabSocket;

class AutocompleteEventCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/autocomplete_event';
        $this->description = 'Autocomplete data for chess events.';
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
        $conf = include(__DIR__.'/../../../config/database.php');

        $params = json_decode(stripslashes($argv[1]), true);

        $key = key($params);

        $values[] = [
            'param' => ":$key",
            'value' => '%'. current($params) .'%',
            'type' => \PDO::PARAM_STR,
        ];

        $sql = "SELECT DISTINCT $key FROM games WHERE $key LIKE :$key LIMIT 10";

        $arr = Pdo::getInstance($conf)
            ->query($sql, $values)
            ->fetchAll(\PDO::FETCH_COLUMN);

        return $socket->getClientStorage()->sendToOne($id, $arr);
    }
}
