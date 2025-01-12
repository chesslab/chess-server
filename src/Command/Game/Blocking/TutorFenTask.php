<?php

namespace ChessServer\Command\Game\Blocking;

use Chess\FenToBoardFactory;
use Chess\Function\CompleteFunction;
use Chess\Tutor\FenEvaluation;
use Chess\Variant\Classical\Board;
use ChessServer\Command\AbstractBlockingTask;

class TutorFenTask extends AbstractBlockingTask
{
    public function run()
    {
        $board = FenToBoardFactory::create($this->params['fen'], new Board());
        $paragraph = (new FenEvaluation(new CompleteFunction(), $board))->paragraph;

        return implode(' ', $paragraph);
    }
}
