<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractCommandContainer;

class CommandContainer extends AbstractCommandContainer
{
    public function __construct()
    {
        $this->obj = new \SplObjectStorage;
        $this->obj->attach(new AutocompleteEventCommand());
        $this->obj->attach(new AutocompletePlayerCommand());
        $this->obj->attach(new StatsOpeningCommand());
    }
}
