<?php

namespace ChessServer\GameMode;

use Chess\Heuristics;
use Chess\Variant\Classical\FEN\StrToBoard;
use ChessServer\Game;
use ChessServer\Command\HeuristicsCommand;

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

    public function res($argv, $cmd)
    {
        try {
            switch (get_class($cmd)) {
                case HeuristicsCommand::class:
                    $movetext = $this->game->getBoard()->getMovetext();
                    if ($this->fen) {
                        $board = (new StrToBoard($this->fen))->create();
                        $heuristics = new Heuristics($movetext, $board);
                    } else {
                        $heuristics = new Heuristics($movetext);
                    }
                    return [
                        $cmd->name => [
                            'evalNames' => $heuristics->getEvalNames(),
                            'balance' => $heuristics->getBalance(),
                        ],
                    ];
                default:
                    return parent::res($argv, $cmd);
            }
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
