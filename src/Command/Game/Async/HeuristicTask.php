<?php

namespace ChessServer\Command\Game\Async;

use Chess\SanHeuristics;
use Chess\Function\CompleteFunction;
use Chess\Variant\Capablanca\Board as CapablancaBoard;
use Chess\Variant\Capablanca\FEN\StrToBoard as CapablancaFenStrToBoard;
use Chess\Variant\CapablancaFischer\Board as CapablancaFischerBoard;
use Chess\Variant\CapablancaFischer\FEN\StrToBoard as CapablancaFischerFenStrToBoard;
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
        } elseif ($this->params['variant'] === CapablancaBoard::VARIANT) {
            $board = isset($this->params['fen'])
                ? (new CapablancaFenStrToBoard($this->params['fen']))->create()
                : new CapablancaBoard();
        } elseif ($this->params['variant'] === CapablancaFischerBoard::VARIANT) {
            $startPos = str_split($this->params['startPos']);
            $board = isset($this->params['fen'])
                ? (new CapablancaFischerFenStrToBoard($this->params['fen'], $startPos))->create()
                : new CapablancaFischerBoard($startPos);
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
