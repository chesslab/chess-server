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
            'event' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(ChesslaBlabSocket $socket, array $argv, int $id)
    {
        $conf = include(__DIR__.'/../../../config/database.php');

        $values[] = [
            'param' => ":Event",
            'value' => '%'. $argv[1] .'%',
            'type' => \PDO::PARAM_STR,
        ];

        $sql = "SELECT DISTINCT Event FROM games WHERE Event LIKE :Event LIMIT 10";

        $arr = Pdo::getInstance($conf)
            ->query($sql, $values)
            ->fetchAll(\PDO::FETCH_COLUMN);

        return $socket->getClientStorage()->sendToOne($id, $arr);
    }
}
