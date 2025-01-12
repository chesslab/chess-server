<?php

namespace ChessServer\Command\Data\Async;

use ChessServer\Command\AbstractDbSyncTask;

class AnnotationsGameTask extends AbstractDbSyncTask
{
    public function run()
    {
        $sql = "SELECT * FROM annotations";

        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
