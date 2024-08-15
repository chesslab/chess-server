<?php

namespace ChessServer\Command\Data;

use ChessServer\Socket\ChesslaBlabSocket;

class AutocompleteWhiteCommand extends DataCommand
{
    public function __construct(Db $db)
    {
        parent::__construct($db);

        $this->name = '/autocomplete_white';
        $this->description = 'Autocomplete data for chess players.';
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

        $key = key($params);

        $values[] = [
            'param' => ":$key",
            'value' => '%'. current($params) .'%',
            'type' => \PDO::PARAM_STR,
        ];

        $sql = "SELECT DISTINCT $key FROM games WHERE $key LIKE :$key LIMIT 10";

        $arr = $this->db->query($sql, $values)->fetchAll(\PDO::FETCH_COLUMN);

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => $arr,
        ]);
    }
}
