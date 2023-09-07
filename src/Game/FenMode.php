<?php

namespace ChessServer\Game;

use ChessServer\Game\Game;

class FenMode extends AbstractMode
{
    const NAME = Game::MODE_FEN;

    protected $fen;

    public function __construct(Game $game, array $resourceIds, string $fen = '')
    {
        parent::__construct($game, $resourceIds);

        $this->fen = $fen;
    }

    public function getFen()
    {
        return $this->fen;
    }
}
