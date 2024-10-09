<?php

namespace ChessServer\Command;

use ChessServer\Db;
use ChessServer\Command\AbstractAsyncTask;

abstract class AbstractDbAsyncTask extends AbstractAsyncTask
{
    protected Db $db;

    public function configure()
    {
        $this->db = new Db($this->env['db']);
    }
}
