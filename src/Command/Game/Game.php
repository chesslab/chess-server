<?php

namespace ChessServer\Command\Game;

use Chess\Computer\GrandmasterMove;
use Chess\UciEngine\UciEngine;
use Chess\UciEngine\Details\Limit;
use Chess\Variant\AbstractBoard;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\StartPosition as Chess960StartPosition;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Classical\PGN\AN\Color;
use Chess\Variant\Dunsany\Board as DunsanyBoard;
use Chess\Variant\Losing\Board as LosingBoard;
use Chess\Variant\RacingKings\Board as RacingKingsBoard;

class Game
{
    const VARIANT_960 = Chess960Board::VARIANT;
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

    private null|GrandmasterMove $gmMove;

    public function __construct(
        string $variant,
        string $mode,
        null|GrandmasterMove $gmMove = null
    ) {
        $this->variant = $variant;
        $this->mode = $mode;
        $this->gmMove = $gmMove;

        if ($this->variant === self::VARIANT_960) {
            $startPos = (new Chess960StartPosition())->create();
            $this->board = new Chess960Board($startPos);
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

    public function setBoard(AbstractBoard $board): Game
    {
        $this->board = $board;

        return $this;
    }

    public function state(): object
    {
        $history = $this->board->history;
        $end = end($history);

        return (object) [
            'turn' => $this->board->turn,
            'pgn' => $end ? $end['move']['pgn'] : null,
            'castlingAbility' => $this->board->castlingAbility,
            'movetext' => $this->board->movetext(),
            'fen' => $this->board->toFen(),
            'isCapture' => $end ? $end['move']['isCapture'] : false,
            'isCheck' => $this->board->isCheck(),
            'mode' => $this->getMode(),
            'end' => $this->end(),
        ];
    }

    public function computer(array $options = [], array $params = []): ?array
    {
        if ($this->gmMove) {
            if ($move = $this->gmMove->move($this->board)) {
                return $move;
            }
        }

        $limit = new Limit();
        $limit->depth = $params['depth'];
        $stockfish = (new UciEngine('/usr/games/stockfish'))->setOption('Skill Level', $options['Skill Level']);
        $analysis = $stockfish->analysis($this->board, $limit);

        $clone = $this->board->clone();
        $clone->playLan($this->board->turn, $analysis['bestmove']);
        $history = $clone->history;
        $end = end($history);

        return [
            'pgn' => $end['move']['pgn'],
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

    protected function end(): array
    {
        if ($this->board->doesWin()) {
            return [
                'msg' => "It's a win",
            ];
        } elseif ($this->board->doesDraw()) {
            return [
                'msg' => "It's a draw",
            ];
        } elseif ($this->board->isMate()) {
            return [
                'msg' => $this->board->turn === Color::B ? 'White wins' : 'Black wins',
            ];
        } elseif ($this->board->isStalemate()) {
            return [
                'msg' => "Draw by stalemate",
            ];
        } elseif ($this->board->isFivefoldRepetition()) {
            return [
                'msg' => "Draw by fivefold repetition",
            ];
        } elseif ($this->board->isFiftyMoveDraw()) {
            return [
                'msg' => "Draw by the fifty-move rule",
            ];
        } elseif ($this->board->isDeadPositionDraw()) {
            return [
                'msg' => "Draw by dead position",
            ];
        }

        return [];
    }
}
