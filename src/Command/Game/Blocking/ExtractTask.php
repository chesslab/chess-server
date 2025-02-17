<?php

namespace ChessServer\Command\Game\Blocking;

use Chess\FenToBoardFactory;
use Chess\SanExtractor;
use Chess\Eval\FastFunction;
use Chess\Variant\VariantType;
use Chess\Variant\Capablanca\Board as CapablancaBoard;
use Chess\Variant\CapablancaFischer\Board as CapablancaFischerBoard;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Classical\Board as ClassicalBoard;
use ChessServer\Command\AbstractBlockingTask;

class ExtractTask extends AbstractBlockingTask
{
    public function run()
    {
        $f = new FastFunction();
        
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

        $steinitz = SanExtractor::steinitz($f, $board->clone(), $this->params['movetext']);

        return FastFunction::normalize(-1, 1, $steinitz);
    }
}
