<?php

namespace ChessServer\Command\Game\Sync;

use Chess\FenToBoardFactory;
use Chess\Function\CompleteFunction;
use Chess\Tutor\FenEvaluation;
use Chess\Variant\Classical\Board;
use ChessServer\Command\AbstractSyncTask;

class TutorFenTask extends AbstractSyncTask
{
    public function run()
    {
        $board = FenToBoardFactory::create($this->params['fen'], new Board());
        $paragraph = (new FenEvaluation(new CompleteFunction(), $board))->paragraph;

        return implode(' ', $paragraph);
    }
}
