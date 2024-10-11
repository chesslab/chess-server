<?php

namespace ChessServer\Command\Game;

use ChessServer\Command\AbstractCli;
use ChessServer\Command\Game\Sync\AcceptPlayRequestCommand;
use ChessServer\Command\Game\Sync\DrawCommand;
use ChessServer\Command\Game\Sync\EvalNamesCommand;
use Spatie\Async\Pool;

class Cli extends AbstractCli
{
    public function __construct(Pool $pool)
    {
        parent::__construct();

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
        $this->commands->attach((new LeaveCommand())->setPool($pool));
        $this->commands->attach(new LegalCommand());
        $this->commands->attach((new PlayLanCommand())->setPool($pool));
        $this->commands->attach((new PlayRavCommand())->setPool($pool));
        $this->commands->attach(new RandomizerCommand());
        $this->commands->attach((new ResignCommand())->setPool($pool));
        $this->commands->attach((new RestartCommand())->setPool($pool));
        $this->commands->attach((new StartCommand())->setPool($pool));
        $this->commands->attach((new StockfishCommand())->setPool($pool));
        $this->commands->attach(new TutorFenCommand());
    }
}
