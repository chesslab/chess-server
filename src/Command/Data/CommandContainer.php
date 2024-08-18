<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractCommandContainer;

class CommandContainer extends AbstractCommandContainer
{
    private Db $db;

    public function __construct($logger)
    {
        parent::__construct($logger);

        $conf = include(__DIR__.'/../../../config/database.php');
        $this->db = new Db($conf);
        $this->obj->attach(new AnnotationsGameCommand($this->db));
        $this->obj->attach(new AutocompleteBlackCommand($this->db));
        $this->obj->attach(new AutocompleteEventCommand($this->db));
        $this->obj->attach(new AutocompleteWhiteCommand($this->db));
        $this->obj->attach(new SearchCommand($this->db));
        $this->obj->attach(new StatsEventCommand($this->db));
        $this->obj->attach(new StatsOpeningCommand($this->db));
        $this->obj->attach(new StatsPlayerCommand($this->db));
    }
}
