<?php

namespace ChessServer\Command\Auth;

use ChessServer\Command\AbstractCli;
use ChessServer\Command\Db;

class Cli extends AbstractCli
{
    private Db $db;

    public function __construct(Db $db)
    {
        parent::__construct();

        $this->db = $db;
        $this->commands->attach(new TotpRefreshCommand($db));
        $this->commands->attach(new TotpSignInCommand($db));
        $this->commands->attach(new TotpSignUpCommand($db));
    }

    public function getDb(): Db
    {
        return $this->db;
    }
}
