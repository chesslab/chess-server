<?php

namespace ChessServer\Command;

use ChessServer\Db;

abstract class AbstractDbAsyncTask extends AbstractAsyncTask
{
    protected Db $db;

    public function configure()
    {
        $this->db = new Db($this->env['db']);
    }
}
