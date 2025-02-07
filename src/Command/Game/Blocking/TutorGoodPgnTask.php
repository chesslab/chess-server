<?php

namespace ChessServer\Command\Game\Blocking;

use Chess\Eval\CompleteFunction;
use Chess\Tutor\GoodPgnEvaluation;
use Chess\UciEngine\UciEngine;
use Chess\UciEngine\Details\Limit;
use Chess\Variant\Classical\Board;
use ChessServer\Command\AbstractBlockingTask;
use ChessServer\Socket\AbstractSocket;

class TutorGoodPgnTask extends AbstractBlockingTask
{
    private Board $board;

    public function __construct(Board $board)
    {
        parent::__construct();

        $this->board = $board;
    }

    public function run()
    {
        $limit = new Limit();
        $limit->depth = 12;
        $stockfish = new UciEngine('/usr/games/stockfish');
        $f = new CompleteFunction();

        $goodPgnEvaluation = new GoodPgnEvaluation($limit, $stockfish, $f, $this->board);

        return [
            'pgn' => $goodPgnEvaluation->pgn,
            'paragraph' => implode(' ', $goodPgnEvaluation->paragraph),
        ];
    }
}
