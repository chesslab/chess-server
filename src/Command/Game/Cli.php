<?php

namespace ChessServer\Command\Game;

use ChessServer\Command\AbstractCli;
use ChessServer\Command\Game\Blocking\ExtractCommand;
use ChessServer\Command\Game\Blocking\LeaveCommand;
use ChessServer\Command\Game\Blocking\PlayCommand;
use ChessServer\Command\Game\Blocking\PlayLanCommand;
use ChessServer\Command\Game\Blocking\PlayRavCommand;
use ChessServer\Command\Game\Blocking\PlotCommand;
use ChessServer\Command\Game\Blocking\RecognizeCommand;
use ChessServer\Command\Game\Blocking\ResignCommand;
use ChessServer\Command\Game\Blocking\RestartCommand;
use ChessServer\Command\Game\Blocking\StockfishCommand;
use ChessServer\Command\Game\NonBlocking\AcceptPlayRequestCommand;
use ChessServer\Command\Game\NonBlocking\AsciiCommand;
use ChessServer\Command\Game\NonBlocking\DrawCommand;
use ChessServer\Command\Game\NonBlocking\EvalNamesCommand;
use ChessServer\Command\Game\NonBlocking\LegalCommand;
use ChessServer\Command\Game\NonBlocking\OnlineGamesCommand;
use ChessServer\Command\Game\NonBlocking\RandomizerCommand;
use ChessServer\Command\Game\NonBlocking\RematchCommand;
use ChessServer\Command\Game\NonBlocking\StartCommand;
use ChessServer\Command\Game\NonBlocking\TakebackCommand;
use ChessServer\Command\Game\NonBlocking\TutorFenCommand;
use ChessServer\Command\Game\NonBlocking\UndoCommand;
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
        $this->commands->attach((new LeaveCommand())->setPool($pool));
        $this->commands->attach(new LegalCommand());
        $this->commands->attach((new PlayCommand())->setPool($pool));
        $this->commands->attach((new PlayLanCommand())->setPool($pool));
        $this->commands->attach((new PlayRavCommand())->setPool($pool));
        $this->commands->attach((new PlotCommand())->setPool($pool));
        $this->commands->attach(new RandomizerCommand());
        $this->commands->attach((new RecognizeCommand())->setPool($pool));
        $this->commands->attach((new ResignCommand())->setPool($pool));
        $this->commands->attach((new RestartCommand())->setPool($pool));
        $this->commands->attach(new StartCommand());
        $this->commands->attach((new StockfishCommand())->setPool($pool));
        $this->commands->attach(new TutorFenCommand());
    }
}
