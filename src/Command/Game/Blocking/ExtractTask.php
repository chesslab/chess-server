<?php

namespace ChessServer\Command\Game\Blocking;

use Chess\SanExtractor;
use Chess\Eval\FastFunction;
use Chess\Variant\VariantType;
use Chess\Variant\Capablanca\FenToBoardFactory as CapablancaFenToBoardFactory;
use Chess\Variant\CapablancaFischer\FenToBoardFactory as CapablancaFischerFenToBoardFactory;
use Chess\Variant\Chess960\FenToBoardFactory as Chess960FenToBoardFactory;
use Chess\Variant\Classical\FenToBoardFactory as ClassicalFenToBoardFactory;
use ChessServer\Command\AbstractBlockingTask;

class ExtractTask extends AbstractBlockingTask
{
    public function run()
    {
        $f = new FastFunction();
        
        if ($this->params['variant'] === VariantType::CHESS_960) {
            $board = isset($this->params['fen'])
                ? Chess960FenToBoardFactory::create($this->params['fen'])
                : Chess960FenToBoardFactory::create();
        } elseif ($this->params['variant'] === VariantType::CAPABLANCA) {
            $board = isset($this->params['fen'])
                ? CapablancaFenToBoardFactory::create($this->params['fen'])
                : CapablancaFenToBoardFactory::create();
        } elseif ($this->params['variant'] === VariantType::CAPABLANCA_FISCHER) {
            $board = isset($this->params['fen'])
                ? CapablancaFischerFenToBoardFactory::create($this->params['fen'])
                : CapablancaFischerFenToBoardFactory::create();
        } elseif ($this->params['variant'] === VariantType::CLASSICAL) {
            $board = isset($this->params['fen'])
                ? ClassicalFenToBoardFactory::create($this->params['fen'])
                : ClassicalFenToBoardFactory::create();
        }

        $steinitz = SanExtractor::steinitz($f, $board->clone(), $this->params['movetext']);

        return FastFunction::normalize(-1, 1, $steinitz);
    }
}
