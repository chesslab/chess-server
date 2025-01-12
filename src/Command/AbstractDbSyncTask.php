<?php

namespace ChessServer\Command;

use ChessServer\Db;

abstract class AbstractDbSyncTask extends AbstractSyncTask
{
    protected Db $db;

    public function configure()
    {
        $this->db = new Db($this->env['db']);
    }
}
