<?php

namespace ChessServer\Command\Data\Sync;

use ChessServer\Command\AbstractDbSyncTask;

class AutocompleteEventTask extends AbstractDbSyncTask
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
