<?php

namespace ChessServer\Command\Game\Sync;

use Chess\FenToBoardFactory;
use Chess\SanExtractor;
use Chess\Function\CompleteFunction;
use Chess\Variant\Capablanca\Board as CapablancaBoard;
use Chess\Variant\CapablancaFischer\Board as CapablancaFischerBoard;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Classical\Board as ClassicalBoard;
use ChessServer\Command\AbstractSyncTask;

class ExtractTask extends AbstractSyncTask
{
    public function run()
    {
        $f = new CompleteFunction();
        
        if ($this->params['variant'] === Chess960Board::VARIANT) {
            $startPos = str_split($this->params['startPos']);
            $board = isset($this->params['fen'])
                ? FenToBoardFactory::create($this->params['fen'], new Chess960Board($startPos))
                : new Chess960Board($startPos);
        } elseif ($this->params['variant'] === CapablancaBoard::VARIANT) {
            $board = isset($this->params['fen'])
                ? FenToBoardFactory::create($this->params['fen'], new CapablancaBoard())
                : new CapablancaBoard();
        } elseif ($this->params['variant'] === CapablancaFischerBoard::VARIANT) {
            $startPos = str_split($this->params['startPos']);
            $board = isset($this->params['fen'])
                ? FenToBoardFactory::create($this->params['fen'], new CapablancaFischerBoard($startPos))
                : new CapablancaFischerBoard($startPos);
        } elseif ($this->params['variant'] === ClassicalBoard::VARIANT) {
            $board = isset($this->params['fen'])
                ? FenToBoardFactory::create($this->params['fen'], new ClassicalBoard())
                : new ClassicalBoard();
        }

        return [
            'steinitz' => SanExtractor::steinitz($f, $board->clone(), $this->params['movetext']),
            'mean' => SanExtractor::mean($f, $board->clone(), $this->params['movetext']),
            'sd' => SanExtractor::sd($f, $board->clone(), $this->params['movetext']),
        ];
    }
}
