<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractCli;

class CommandContainer extends AbstractCli
{
    private Db $db;

    public function __construct(Db $db)
    {
        parent::__construct();

        $this->db = $db;
        $this->commands->attach(new AnnotationsGameCommand($db));
        $this->commands->attach(new AutocompleteBlackCommand($db));
        $this->commands->attach(new AutocompleteEventCommand($db));
        $this->commands->attach(new AutocompleteWhiteCommand($db));
        $this->commands->attach(new SearchCommand($db));
        $this->commands->attach(new ResultEventCommand($db));
        $this->commands->attach(new ResultPlayerCommand($db));
        $this->commands->attach(new ResultCommand($db));
    }

    public function getDb(): Db
    {
        return $this->db;
    }
}
