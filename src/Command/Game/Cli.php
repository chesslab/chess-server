<?php

namespace ChessServer\Command\Game;

use ChessServer\Command\AbstractCli;
use ChessServer\Command\Game\Blocking\ExtractCommand;
use ChessServer\Command\Game\Blocking\HeuristicCommand;
use ChessServer\Command\Game\Blocking\LeaveCommand;
use ChessServer\Command\Game\Blocking\PlayCommand;
use ChessServer\Command\Game\Blocking\PlayLanCommand;
use ChessServer\Command\Game\Blocking\PlayRavCommand;
use ChessServer\Command\Game\Blocking\RecognizerCommand;
use ChessServer\Command\Game\Blocking\ResignCommand;
use ChessServer\Command\Game\Blocking\RestartCommand;
use ChessServer\Command\Game\Blocking\StockfishCommand;
use ChessServer\Command\Game\Blocking\TutorFenCommand;
use ChessServer\Command\Game\Async\AcceptPlayRequestCommand;
use ChessServer\Command\Game\Async\AsciiCommand;
use ChessServer\Command\Game\Async\DrawCommand;
use ChessServer\Command\Game\Async\EvalNamesCommand;
use ChessServer\Command\Game\Async\LegalCommand;
use ChessServer\Command\Game\Async\OnlineGamesCommand;
use ChessServer\Command\Game\Async\RandomizerCommand;
use ChessServer\Command\Game\Async\RematchCommand;
use ChessServer\Command\Game\Async\StartCommand;
use ChessServer\Command\Game\Async\TakebackCommand;
use ChessServer\Command\Game\Async\UndoCommand;
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
        $this->commands->attach((new ExtractCommand())->setPool($pool));
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
