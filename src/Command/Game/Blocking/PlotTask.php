<?php

namespace ChessServer\Command\Game\Blocking;

use Chess\SanPlotter;
use Chess\Eval\CompleteFunction;
use Chess\Variant\VariantType;
use Chess\Variant\Capablanca\Board as CapablancaBoard;
use Chess\Variant\Capablanca\FenToBoardFactory as CapablancaFenToBoardFactory;
use Chess\Variant\CapablancaFischer\Board as CapablancaFischerBoard;
use Chess\Variant\CapablancaFischer\FenToBoardFactory as CapablancaFischerFenToBoardFactory;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\FenToBoardFactory as Chess960FenToBoardFactory;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Classical\FenToBoardFactory as ClassicalFenToBoardFactory;
use ChessServer\Command\AbstractBlockingTask;

class PlotTask extends AbstractBlockingTask
{
    public function run()
    {
        if ($this->params['variant'] === VariantType::CHESS_960) {
            $board = isset($this->params['fen'])
                ? Chess960FenToBoardFactory::create($this->params['fen'])
                : new Chess960Board();
        } elseif ($this->params['variant'] === VariantType::CAPABLANCA) {
            $board = isset($this->params['fen'])
                ? CapablancaFenToBoardFactory::create($this->params['fen'])
                : new CapablancaBoard();
        } elseif ($this->params['variant'] === VariantType::CAPABLANCA_FISCHER) {
            $board = isset($this->params['fen'])
                ? CapablancaFischerFenToBoardFactory::create($this->params['fen'])
                : new CapablancaFischerBoard();
        } elseif ($this->params['variant'] === VariantType::CLASSICAL) {
            $board = isset($this->params['fen'])
                ? FenToBoardFactory::create($this->params['fen'])
                : new ClassicalBoard();
        }

        $time = SanPlotter::time(
            new CompleteFunction(),
            $board,
            $this->params['movetext'],
            $this->params['name']
        );

        return $time;
    }
}
