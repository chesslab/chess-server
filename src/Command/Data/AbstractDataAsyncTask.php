<?php

namespace ChessServer\Command\Data;

use ChessServer\Db;
use ChessServer\Command\AbstractAsyncTask;

abstract class AbstractDataAsyncTask extends AbstractAsyncTask
{
    protected Db $db;

    public function configure()
    {
        $this->db = new Db($this->env['db']);
    }
}
