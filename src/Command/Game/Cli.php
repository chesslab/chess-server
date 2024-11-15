<?php

namespace ChessServer\Command\Game;

use ChessServer\Command\AbstractCli;
use ChessServer\Command\Game\Async\HeuristicCommand;
use ChessServer\Command\Game\Async\LeaveCommand;
use ChessServer\Command\Game\Async\PlayCommand;
use ChessServer\Command\Game\Async\PlayLanCommand;
use ChessServer\Command\Game\Async\PlayRavCommand;
use ChessServer\Command\Game\Async\RecognizerCommand;
use ChessServer\Command\Game\Async\ResignCommand;
use ChessServer\Command\Game\Async\RestartCommand;
use ChessServer\Command\Game\Async\StockfishCommand;
use ChessServer\Command\Game\Async\TutorFenCommand;
use ChessServer\Command\Game\Sync\AcceptPlayRequestCommand;
use ChessServer\Command\Game\Sync\AsciiCommand;
use ChessServer\Command\Game\Sync\DrawCommand;
use ChessServer\Command\Game\Sync\EvalNamesCommand;
use ChessServer\Command\Game\Sync\LegalCommand;
use ChessServer\Command\Game\Sync\OnlineGamesCommand;
use ChessServer\Command\Game\Sync\RandomizerCommand;
use ChessServer\Command\Game\Sync\RematchCommand;
use ChessServer\Command\Game\Sync\StartCommand;
use ChessServer\Command\Game\Sync\TakebackCommand;
use ChessServer\Command\Game\Sync\UndoCommand;
use Spatie\Async\Pool;

class Cli extends AbstractCli
{
    public function __construct(Pool $pool)
    {
        parent::__construct();

        // text-based commands
        $this->commands->attach(new AsciiCommand());
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
        $this->commands->attach((new PlayCommand())->setPool($pool));
        $this->commands->attach((new PlayLanCommand())->setPool($pool));
        $this->commands->attach((new PlayRavCommand())->setPool($pool));
        $this->commands->attach(new RandomizerCommand());
        $this->commands->attach((new RecognizerCommand())->setPool($pool));
        $this->commands->attach((new ResignCommand())->setPool($pool));
        $this->commands->attach((new RestartCommand())->setPool($pool));
        $this->commands->attach(new StartCommand());
        $this->commands->attach((new StockfishCommand())->setPool($pool));
        $this->commands->attach((new TutorFenCommand())->setPool($pool));
    }
}
