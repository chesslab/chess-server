<?php

namespace ChessServer\Command\Game;

use Chess\Variant\AbstractBoard;
use Chess\Variant\Capablanca\Board as CapablancaBoard;
use Chess\Variant\CapablancaFischer\Board as CapablancaFischerBoard;
use Chess\Variant\CapablancaFischer\StartPosition as CapablancaFischerStartPosition;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\StartPosition as Chess960StartPosition;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Classical\PGN\AN\Color;
use Chess\Variant\Classical\PGN\AN\Termination;
use Chess\Variant\Dunsany\Board as DunsanyBoard;
use Chess\Variant\Losing\Board as LosingBoard;
use Chess\Variant\RacingKings\Board as RacingKingsBoard;

class Game
{
    const VARIANT_960 = Chess960Board::VARIANT;
    const VARIANT_CAPABLANCA = CapablancaBoard::VARIANT;
    const VARIANT_CAPABLANCA_FISCHER = CapablancaFischerBoard::VARIANT;
    const VARIANT_CLASSICAL = ClassicalBoard::VARIANT;
    const VARIANT_DUNSANY = DunsanyBoard::VARIANT;
    const VARIANT_LOSING = LosingBoard::VARIANT;
    const VARIANT_RACING_KINGS = RacingKingsBoard::VARIANT;

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

        if ($this->variant === self::VARIANT_960) {
            $startPos = (new Chess960StartPosition())->create();
            $this->board = new Chess960Board($startPos);
        } elseif ($this->variant === self::VARIANT_CAPABLANCA) {
            $this->board = new CapablancaBoard();
        } elseif ($this->variant === self::VARIANT_CAPABLANCA_FISCHER) {
            $startPos = (new CapablancaFischerStartPosition())->create();
            $this->board = new CapablancaFischerBoard($startPos);
        } elseif ($this->variant === self::VARIANT_CLASSICAL) {
            $this->board = new ClassicalBoard();
        } elseif ($this->variant === self::VARIANT_DUNSANY) {
            $this->board = new DunsanyBoard();
        } elseif ($this->variant === self::VARIANT_LOSING) {
            $this->board = new LosingBoard();
        } elseif ($this->variant === self::VARIANT_RACING_KINGS) {
            $this->board = new RacingKingsBoard();
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
