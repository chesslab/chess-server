<?php

namespace ChessServer\Command\Game\Async;

use Chess\FenToBoardFactory;
use Chess\Play\RavPlay;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Classical\Board as ClassicalBoard;
use ChessServer\Command\AbstractAsyncTask;

class PlayRavTask extends AbstractAsyncTask
{
    public function run()
    {
        if ($this->params['variant'] === Chess960Board::VARIANT) {
            $startPos = str_split($this->params['startPos']);
            $board = new Chess960Board($startPos);
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
            ...($this->params['variant'] === Chess960Board::VARIANT
                ? ['startPos' =>  $this->params['startPos']]
                : []
            ),
        ];
    }
}
