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
        $this->commands->attach((new AnnotationsGameCommand())->setPool($pool));
        $this->commands->attach((new RankingCommand())->setPool($pool));
        $this->commands->attach((new ResultCommand())->setPool($pool));
        // param-based commands
        $this->commands->attach((new AutocompleteBlackCommand())->setPool($pool));
        $this->commands->attach((new AutocompleteEventCommand())->setPool($pool));
        $this->commands->attach((new AutocompleteWhiteCommand())->setPool($pool));
        $this->commands->attach((new ResultEventCommand())->setPool($pool));
        $this->commands->attach((new ResultPlayerCommand())->setPool($pool));
        $this->commands->attach((new SearchCommand())->setPool($pool));
    }

    public function getDb(): Db
    {
        return $this->db;
    }
}
