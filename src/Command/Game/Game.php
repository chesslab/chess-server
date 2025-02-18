<?php

namespace ChessServer\Command\Game;

use Chess\Variant\AbstractBoard;
use Chess\Variant\VariantType;
use Chess\Variant\Capablanca\FenToBoardFactory as CapablancaFenToBoardFactory;
use Chess\Variant\CapablancaFischer\FenToBoardFactory as CapablancaFischerFenToBoardFactory;
use Chess\Variant\Chess960\FenToBoardFactory as Chess960FenToBoardFactory;
use Chess\Variant\Classical\FenToBoardFactory as ClassicalFenToBoardFactory;
use Chess\Variant\Classical\PGN\Color;
use Chess\Variant\Classical\PGN\Termination;
use Chess\Variant\Dunsany\FenToBoardFactory as DunsanyFenToBoardFactory;
use Chess\Variant\Losing\FenToBoardFactory as LosingFenToBoardFactory;
use Chess\Variant\RacingKings\FenToBoardFactory as RacingKingsFenToBoardFactory;

class Game
{
    const MODE_ANALYSIS = 'analysis';
    const MODE_PLAY = 'play';
    const MODE_STOCKFISH = 'stockfish';

    private AbstractBoard $board;

    private string $variant;

    private string $mode;

    private string $resignation = '';

    private string $abandoned = '';

    public function __construct(string $variant, string $mode)
    {
        $this->variant = $variant;
        $this->mode = $mode;

        if ($this->variant === VariantType::CHESS_960) {
            $this->board = Chess960FenToBoardFactory::create();
        } elseif ($this->variant === VariantType::CAPABLANCA) {
            $this->board = CapablancaFenToBoardFactory::create();
        } elseif ($this->variant === VariantType::CAPABLANCA_FISCHER) {
            $this->board = CapablancaFischerFenToBoardFactory::create();
        } elseif ($this->variant === VariantType::CLASSICAL) {
            $this->board = ClassicalFenToBoardFactory::create();
        } elseif ($this->variant === VariantType::DUNSANY) {
            $this->board = DunsanyFenToBoardFactory::create();
        } elseif ($this->variant === VariantType::LOSING) {
            $this->board = LosingFenToBoardFactory::create();
        } elseif ($this->variant === VariantType::RACING_KINGS) {
            $this->board = RacingKingsFenToBoardFactory::create();
        }
    }

    public function getBoard(): AbstractBoard
    {
        return $this->board;
    }

    public function getVariant(): string
    {
        return $this->variant;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getResignation(): string
    {
        return $this->resignation;
    }

    public function getAbandoned(): string
    {
        return $this->abandoned;
    }

    public function setBoard(AbstractBoard $board): Game
    {
        $this->board = $board;

        return $this;
    }

    public function setResignation(string $color): Game
    {
        $this->resignation = $color;

        return $this;
    }

    public function setAbandoned(string $color): Game
    {
        $this->abandoned = $color;

        return $this;
    }

    public function state(): object
    {
        $end = $this->end();

        return (object) [
            'turn' => $this->board->turn,
            'movetext' => $this->board->movetext(),
            'fen' => $this->board->toFen(),
            ...($end
                ? ['end' => $end]
                : []
            ),
        ];
    }

    public function play(string $color, string $pgn): bool
    {
        return $this->board->play($color, $pgn);
    }

    public function playLan(string $color, string $lan): bool
    {
        return $this->board->playLan($color, $lan);
    }

    public function resign(string $color)
    {
        return $color;
    }

    protected function end(): ?array
    {
        if ($this->board->doesWin()) {
            // TODO ...
            return [
                'result' => Termination::UNKNOWN,
                'msg' => "It's a win",
            ];
        } elseif ($this->board->doesDraw()) {
            return [
                'result' => Termination::DRAW,
                'msg' => "It's a draw",
            ];
        } elseif ($this->board->isMate()) {
            return [
                'result' => $this->board->turn === Color::B
                    ? Termination::WHITE_WINS
                    : Termination::BLACK_WINS,
                'msg' => $this->board->turn === Color::B
                    ? 'White wins'
                    : 'Black wins',
            ];
        } elseif ($this->board->isStalemate()) {
            return [
                'result' => Termination::DRAW,
                'msg' => "Draw by stalemate",
            ];
        } elseif ($this->board->isFivefoldRepetition()) {
            return [
                'result' => Termination::DRAW,
                'msg' => "Draw by fivefold repetition",
            ];
        } elseif ($this->board->isFiftyMoveDraw()) {
            return [
                'result' => Termination::DRAW,
                'msg' => "Draw by the fifty-move rule",
            ];
        } elseif ($this->board->isDeadPositionDraw()) {
            return [
                'result' => Termination::DRAW,
                'msg' => "Draw by dead position",
            ];
        } elseif ($this->resignation) {
            return [
                'result' => $this->resignation === Color::B
                    ? Termination::WHITE_WINS
                    : Termination::BLACK_WINS,
                'msg' => $this->resignation === Color::B
                    ? 'White wins'
                    : 'Black wins',
            ];
        } elseif ($this->abandoned) {
            return [
                'result' => $this->abandoned === Color::B
                    ? Termination::WHITE_WINS
                    : Termination::BLACK_WINS,
                'msg' => $this->abandoned === Color::B
                    ? 'White wins'
                    : 'Black wins',
            ];
        }

        return null;
    }
}
