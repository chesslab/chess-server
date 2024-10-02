<?php

namespace ChessServer\Command\Game\AsyncTask;

use Chess\SanHeuristic;
use Chess\Function\CompleteFunction;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\FEN\StrToBoard as Chess960FenStrToBoard;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Classical\FEN\StrToBoard as ClassicalFenStrToBoard;
use Spatie\Async\Task;

class HeuristicAsyncTask extends Task
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

        $balance = (new SanHeuristic(
            new CompleteFunction(),
            $this->params['name'],
            $this->params['movetext'],
            $board
        ))->getBalance();

        return $balance;
    }
}
