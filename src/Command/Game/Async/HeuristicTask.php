<?php

namespace ChessServer\Command\Game\Async;

use Chess\SanHeuristics;
use Chess\Function\CompleteFunction;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\FEN\StrToBoard as Chess960FenStrToBoard;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Classical\FEN\StrToBoard as ClassicalFenStrToBoard;
use ChessServer\Command\AbstractAsyncTask;

class HeuristicTask extends AbstractAsyncTask
{
    public function run()
    {
        if ($this->params['variant'] === Chess960Board::VARIANT) {
            $startPos = str_split($this->params['startPos']);
            $board = isset($this->params['fen'])
                ? (new Chess960FenStrToBoard($this->params['fen'], $startPos))->create()
                : new Chess960Board($startPos);
        } elseif ($this->params['variant'] === ClassicalBoard::VARIANT) {
            $board = isset($this->params['fen'])
                ? (new ClassicalFenStrToBoard($this->params['fen']))->create()
                : new ClassicalBoard();
        }

        $balance = (new SanHeuristics(
            new CompleteFunction(),
            $this->params['movetext'],
            $this->params['name'],
            $board
        ))->getBalance();

        return $balance;
    }
}
