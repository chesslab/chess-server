<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractCommandContainer;
use Monolog\Logger;

class CommandContainer extends AbstractCommandContainer
{
    private Db $db;

    public function __construct(Db $db, Logger $logger)
    {
        parent::__construct($logger);

        $this->db = $db;
        $this->obj->attach(new AnnotationsGameCommand($db));
        $this->obj->attach(new AutocompleteBlackCommand($db));
        $this->obj->attach(new AutocompleteEventCommand($db));
        $this->obj->attach(new AutocompleteWhiteCommand($db));
        $this->obj->attach(new SearchCommand($db));
        $this->obj->attach(new StatsEventCommand($db));
        $this->obj->attach(new StatsOpeningCommand($db));
        $this->obj->attach(new StatsPlayerCommand($db));
    }
}
