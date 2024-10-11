<?php

namespace ChessServer\Command\Binary;

use ChessServer\Command\AbstractCli;
use ChessServer\Command\Binary\Async\ImageCommand;
use Spatie\Async\Pool;

class Cli extends AbstractCli
{
    public function __construct(Pool $pool)
    {
        parent::__construct();

        $this->commands->attach((new ImageCommand())->setPool($pool));
    }
}
