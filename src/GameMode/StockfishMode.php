<?php

namespace ChessServer\GameMode;

use ChessServer\Game;

class StockfishMode extends AbstractMode
{
    const NAME = Game::MODE_STOCKFISH;

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
