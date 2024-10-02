<?php

namespace ChessServer\Command\Game;

use Spatie\Async\Task;

class StockfishAsyncTask extends Task
{
    private $params;

    private $gameMode;

    private $command;

    public function __construct($params, $gameMode, $command)
    {
        $this->params = $params;
        $this->gameMode = $gameMode;
        $this->command = $command;

    }

    public function configure()
    {
    }

    public function run()
    {
        return $this->gameMode->res($this->params, $this->command);
    }
}
