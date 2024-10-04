<?php

namespace ChessServer\Command\Auth;

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
        $this->commands->attach(new TotpRefreshCommand($db));
        $this->commands->attach(new TotpSignInCommand($db));
        $this->commands->attach((new TotpSignUpCommand())->setPool($pool));
    }

    public function getDb(): Db
    {
        return $this->db;
    }
}
