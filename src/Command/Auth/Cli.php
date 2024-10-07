<?php

namespace ChessServer\Command\Auth;

use ChessServer\Command\AbstractCli;
use Spatie\Async\Pool;

class Cli extends AbstractCli
{
    public function __construct(Pool $pool)
    {
        parent::__construct();

        $this->commands->attach((new TotpRefreshCommand())->setPool($pool));
        $this->commands->attach((new TotpSignInCommand())->setPool($pool));
        $this->commands->attach((new TotpSignUpCommand())->setPool($pool));
    }
}
