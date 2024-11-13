<?php

namespace ChessServer\Command\Data\Async;

use ChessServer\Command\AbstractDbAsyncTask;

class AnnotationsGameTask extends AbstractDbAsyncTask
{
    public function run()
    {
        $sql = "SELECT * FROM annotations";

        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
