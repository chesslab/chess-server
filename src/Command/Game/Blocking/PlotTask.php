<?php

namespace ChessServer\Command\Game\Blocking;

use Chess\FenToBoardFactory;
use Chess\SanPlotter;
use Chess\Eval\CompleteFunction;
use Chess\Variant\VariantType;
use Chess\Variant\Capablanca\Board as CapablancaBoard;
use Chess\Variant\CapablancaFischer\Board as CapablancaFischerBoard;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Classical\Board as ClassicalBoard;
use ChessServer\Command\AbstractBlockingTask;

class PlotTask extends AbstractBlockingTask
{
    public function run()
    {
        if ($this->params['variant'] === VariantType::CHESS_960) {
            $shuffle = str_split($this->params['shuffle']);
            $board = isset($this->params['fen'])
                ? FenToBoardFactory::create($this->params['fen'], new Chess960Board($shuffle))
                : new Chess960Board($shuffle);
        } elseif ($this->params['variant'] === VariantType::CAPABLANCA) {
            $board = isset($this->params['fen'])
                ? FenToBoardFactory::create($this->params['fen'], new CapablancaBoard())
                : new CapablancaBoard();
        } elseif ($this->params['variant'] === VariantType::CAPABLANCA_FISCHER) {
            $shuffle = str_split($this->params['shuffle']);
            $board = isset($this->params['fen'])
                ? FenToBoardFactory::create($this->params['fen'], new CapablancaFischerBoard($shuffle))
                : new CapablancaFischerBoard($shuffle);
        } elseif ($this->params['variant'] === VariantType::CLASSICAL) {
            $board = isset($this->params['fen'])
                ? FenToBoardFactory::create($this->params['fen'], new ClassicalBoard())
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
