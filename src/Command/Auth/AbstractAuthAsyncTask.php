<?php

namespace ChessServer\Command\Auth;

use ChessServer\Db;
use ChessServer\Command\AbstractAsyncTask;

abstract class AbstractAuthAsyncTask extends AbstractAsyncTask
{
    protected Db $db;

    public function configure()
    {
        $this->db = new Db($this->env['db']);
    }
}
