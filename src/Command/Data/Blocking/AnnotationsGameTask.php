<?php

namespace ChessServer\Command\Data\Blocking;

use ChessServer\Command\AbstractDbBlockingTask;

class AnnotationsGameTask extends AbstractDbBlockingTask
{
    public function run()
    {
        $sql = "SELECT * FROM annotations";

        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
