<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractCli;
use ChessServer\Command\Data\Blocking\AnnotationsGameCommand;
use ChessServer\Command\Data\Blocking\AutocompleteBlackCommand;
use ChessServer\Command\Data\Blocking\AutocompleteEventCommand;
use ChessServer\Command\Data\Blocking\AutocompleteWhiteCommand;
use ChessServer\Command\Data\Blocking\OpeningCommand;
use ChessServer\Command\Data\Blocking\RankingCommand;
use ChessServer\Command\Data\Blocking\SearchCommand;
use Spatie\NonBlocking\Pool;

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
