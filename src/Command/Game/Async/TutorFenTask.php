<?php

namespace ChessServer\Command\Game\Async;

use Chess\FenToBoardFactory;
use Chess\Function\CompleteFunction;
use Chess\Tutor\FenEvaluation;
use Chess\Variant\Classical\Board;
use Spatie\Async\Task;

class TutorFenTask extends Task
{
    private array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function configure()
    {
    }

    public function run()
    {
        $board = FenToBoardFactory::create($this->params['fen'], new Board());
        $paragraph = (new FenEvaluation(new CompleteFunction(), $board))->paragraph;

        return $paragraph;
    }
}
