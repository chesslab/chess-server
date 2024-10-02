<?php

namespace ChessServer\Command\Game;

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
        $this->commands->attach(new EvalNamesCommand());
        $this->commands->attach(new OnlineGamesCommand());
        $this->commands->attach(new UndoCommand());
        // action-based commands
        $this->commands->attach(new DrawCommand());
        $this->commands->attach(new RematchCommand());
        $this->commands->attach(new TakebackCommand());
        // param-based commands
        $this->commands->attach(new AcceptPlayRequestCommand());
        $this->commands->attach((new HeuristicCommand())->setPool($pool));
        $this->commands->attach(new LeaveCommand($db));
        $this->commands->attach(new LegalCommand());
        $this->commands->attach(new PlayLanCommand());
        $this->commands->attach(new PlayRavCommand());
        $this->commands->attach(new RandomizerCommand());
        $this->commands->attach(new ResignCommand($db));
        $this->commands->attach(new RestartCommand($db));
        $this->commands->attach(new StartCommand($db));
        $this->commands->attach(new StockfishCommand());
        $this->commands->attach(new TutorFenCommand());
    }

    public function getDb(): Db
    {
        return $this->db;
    }
}
