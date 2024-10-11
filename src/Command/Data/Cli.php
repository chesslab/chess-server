<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractCli;
use ChessServer\Command\Data\Async\AnnotationsGameCommand;
use ChessServer\Command\Data\Async\AutocompleteBlackCommand;
use ChessServer\Command\Data\Async\AutocompleteEventCommand;
use ChessServer\Command\Data\Async\AutocompleteWhiteCommand;
use ChessServer\Command\Data\Async\RankingCommand;
use ChessServer\Command\Data\Async\ResultCommand;
use ChessServer\Command\Data\Async\ResultEventCommand;
use Spatie\Async\Pool;

class Cli extends AbstractCli
{
    public function __construct(Pool $pool)
    {
        parent::__construct();

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
}
