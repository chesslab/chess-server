<?php

namespace ChessServer\Command\Game\Blocking;

use Chess\FenToBoardFactory;
use Chess\Play\RavPlay;
use Chess\Variant\VariantType;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Classical\Board as ClassicalBoard;
use ChessServer\Command\AbstractBlockingTask;

class PlayRavTask extends AbstractBlockingTask
{
    public function run()
    {
        if ($this->params['variant'] === VariantType::CHESS_960) {
            $shuffle = str_split($this->params['shuffle']);
            $board = new Chess960Board($shuffle);
            if (isset($this->params['fen'])) {
                $board = FenToBoardFactory::create($this->params['fen'], $board);
            }
            $ravPlay = new RavPlay($this->params['movetext'], $board);
        } else {
            $board = new ClassicalBoard();
            if (isset($this->params['fen'])) {
                $board = FenToBoardFactory::create($this->params['fen'], $board);
            }
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
