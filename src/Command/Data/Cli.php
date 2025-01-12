<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractCli;
use ChessServer\Command\Data\Sync\AnnotationsGameCommand;
use ChessServer\Command\Data\Sync\AutocompleteBlackCommand;
use ChessServer\Command\Data\Sync\AutocompleteEventCommand;
use ChessServer\Command\Data\Sync\AutocompleteWhiteCommand;
use ChessServer\Command\Data\Sync\OpeningCommand;
use ChessServer\Command\Data\Sync\RankingCommand;
use ChessServer\Command\Data\Sync\SearchCommand;
use Spatie\Async\Pool;

class Cli extends AbstractCli
{
    public function __construct(Pool $pool)
    {
        parent::__construct();

        // text-based commands
        $this->commands->attach((new AnnotationsGameCommand())->setPool($pool));
        $this->commands->attach((new RankingCommand())->setPool($pool));
        // param-based commands
        $this->commands->attach((new AutocompleteBlackCommand())->setPool($pool));
        $this->commands->attach((new AutocompleteEventCommand())->setPool($pool));
        $this->commands->attach((new AutocompleteWhiteCommand())->setPool($pool));
        $this->commands->attach((new OpeningCommand())->setPool($pool));
        $this->commands->attach((new SearchCommand())->setPool($pool));
    }
}
