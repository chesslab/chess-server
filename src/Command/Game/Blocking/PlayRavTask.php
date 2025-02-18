<?php

namespace ChessServer\Command\Game\Blocking;

use Chess\Play\RavPlay;
use Chess\Variant\VariantType;
use Chess\Variant\Chess960\FenToBoardFactory as Chess960FenToBoardFactory;
use Chess\Variant\Classical\FenToBoardFactory as ClassicalFenToBoardFactory;
use ChessServer\Command\AbstractBlockingTask;

class PlayRavTask extends AbstractBlockingTask
{
    public function run()
    {
        if ($this->params['variant'] === VariantType::CHESS_960) {
            $board = isset($this->params['fen'])
                ? Chess960FenToBoardFactory::create($this->params['fen'])
                : Chess960FenToBoardFactory::create();
            $ravPlay = new RavPlay($this->params['movetext'], $board);
        } else {
            $board = isset($this->params['fen'])
                ? ClassicalFenToBoardFactory::create($this->params['fen'])
                : ClassicalFenToBoardFactory::create();
            $ravPlay = new RavPlay($this->params['movetext'], $board);
        }

        $board = $ravPlay->validate()->board;

        return [
            'variant' => $this->params['variant'],
            'turn' => $board->turn,
            'filtered' => $ravPlay->ravMovetext->filtered(),
            'movetext' => $ravPlay->ravMovetext->main(),
            'breakdown' => $ravPlay->ravMovetext->breakdown,
            'fen' => $ravPlay->fen,
        ];
    }
}
