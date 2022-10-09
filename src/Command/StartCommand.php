<?php

namespace ChessServer\Command;

use Chess\Game;
use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\GameMode\AnalysisMode;
use ChessServer\GameMode\GmMode;
use ChessServer\GameMode\FenMode;
use ChessServer\GameMode\PgnMode;
use ChessServer\GameMode\PlayMode;
use ChessServer\GameMode\StockfishMode;

class StartCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/start';
        $this->description = 'Starts a new game.';
        $this->params = [
            // mandatory param
            'variant' => [
                Game::VARIANT_960,
                Game::VARIANT_CAPABLANCA_80,
                Game::VARIANT_CAPABLANCA_100,
                Game::VARIANT_CLASSICAL,
            ],
            // mandatory param
            'mode' => [
                AnalysisMode::NAME,
                GmMode::NAME,
                FenMode::NAME,
                PgnMode::NAME,
                PlayMode::NAME,
                StockfishMode::NAME,
            ],
            // FenMode
            // optional param
            // StockfishMode
            // optional param
            'fen' => '<string>',
            // PgnMode
            // optional param
            'movetext' => '<string>',
            // GmMode
            // optional param
            // StockfishMode
            // optional param
            'color' => [
                Color::W,
                Color::B,
            ],
            // PlayMode
            // mandatory param
            'settings' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        if (in_array($argv[1], $this->params['variant'])) {
            if (in_array($argv[2], $this->params['mode'])) {
                switch ($argv[2]) {
                    case AnalysisMode::NAME:
                        return count($argv) - 1 === 2;
                    case GmMode::NAME:
                        return count($argv) - 1 === 3;
                    case FenMode::NAME:
                        return count($argv) - 1 === 3;
                    case PgnMode::NAME:
                        return count($argv) - 1 === 3;
                    case PlayMode::NAME:
                        return count($argv) - 1 === 3;
                    case StockfishMode::NAME:
                        return count($argv) - 1 === 3;
                    default:
                        // do nothing
                        break;
                }
            }
        }

        return false;
    }
}
