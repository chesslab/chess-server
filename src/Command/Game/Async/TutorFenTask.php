<?php

namespace ChessServer\Command\Game\Async;

use Chess\FenToBoardFactory;
use Chess\Function\CompleteFunction;
use Chess\Tutor\FenEvaluation;
use Chess\Variant\Classical\Board;
use ChessServer\Command\AbstractAsyncTask;

class TutorFenTask extends AbstractAsyncTask
{
    public function run()
    {
        $board = FenToBoardFactory::create($this->params['fen'], new Board());
        $paragraph = (new FenEvaluation(new CompleteFunction(), $board))->paragraph;

        return implode(' ', $paragraph);
    }
}
