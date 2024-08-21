<?php

namespace ChessServer\Command\Binary;

use ChessServer\Command\AbstractCli;

class Cli extends AbstractCli
{
    public function __construct()
    {
        parent::__construct();

        $this->commands->attach(new ImageCommand());
    }
}
