<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractDbAsyncTask;

class AutocompleteBlackAsyncTask extends AbstractDbAsyncTask
{
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
