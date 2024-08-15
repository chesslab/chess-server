<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractCommand;

abstract class DataCommand extends AbstractCommand
{
    protected Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }
}
