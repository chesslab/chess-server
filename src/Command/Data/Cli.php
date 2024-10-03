<?php

namespace ChessServer\Command\Data;

use ChessServer\Db;
use ChessServer\Command\AbstractCli;
use Spatie\Async\Pool;

class Cli extends AbstractCli
{
    private Db $db;

    public function __construct(Pool $pool, Db $db)
    {
        parent::__construct();

        $this->db = $db;
        // text-based commands
        $this->commands->attach(new AnnotationsGameCommand($db));
        $this->commands->attach(new RankingCommand($db));
        $this->commands->attach(new ResultCommand($db));
        // param-based commands
        $this->commands->attach((new AutocompleteBlackCommand())->setPool($pool));
        $this->commands->attach(new AutocompleteEventCommand($db));
        $this->commands->attach(new AutocompleteWhiteCommand($db));
        $this->commands->attach(new ResultEventCommand($db));
        $this->commands->attach(new ResultPlayerCommand($db));
        $this->commands->attach((new SearchCommand())->setPool($pool));
    }

    public function getDb(): Db
    {
        return $this->db;
    }
}
