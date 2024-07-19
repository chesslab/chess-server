<?php

namespace ChessServer\Game;

use Chess\Computer\GrandmasterMove;
use Chess\UciEngine\UciEngine;
use Chess\UciEngine\Details\Limit;
use Chess\Variant\AbstractBoard;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\StartPosition as Chess960StartPosition;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Dunsany\Board as DunsanyBoard;
use Chess\Variant\Losing\Board as LosingBoard;
use Chess\Variant\RacingKings\Board as RacingKingsBoard;

/**
 * Game
 *
 * @author Jordi Bassagaña
 * @license GPL
 */
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

    /**
     * Chess board.
     *
     * @var \Chess\Variant\AbstractBoard
     */
    private AbstractBoard $board;

    /**
     * Variant.
     *
     * @var string
     */
    private string $variant;

    /**
     * Mode.
     *
     * @var string
     */
    private string $mode;

    /**
     * Grandmaster computer.
     *
     * @var \Chess\Computer\GrandmasterMove
     */
    private null|GrandmasterMove $gmMove;

    /**
     * Constructor.
     *
     * @param string $variant
     * @param string $mode
     * @param GrandmasterMove|null \Chess\Computer\GrandmasterMove|null
     */
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

    /**
     * Returns the chess board object.
     *
     * @return \Chess\Variant\AbstractBoard
     */
    public function getBoard(): AbstractBoard
    {
        return $this->board;
    }

    /**
     * Returns the game variant.
     *
     * @return string
     */
    public function getVariant(): string
    {
        return $this->variant;
    }

    /**
     * Returns the game mode.
     *
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Sets the chess board object.
     *
     * @param \Chess\Variant\AbstractBoard $board
     * @return \ChessServer\Game
     */
    public function setBoard(AbstractBoard $board): Game
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Returns the state of the board.
     *
     * @return object
     */
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
            'isMate' => $this->board->isMate(),
            'isStalemate' => $this->board->isStalemate(),
            'isFivefoldRepetition' => $this->board->isFivefoldRepetition(),
            'isFiftyMoveDraw' => $this->board->isFiftyMoveDraw(),
            'isDeadPositionDraw' => $this->board->isDeadPositionDraw(),
            'doesDraw' => $this->board->doesDraw(),
            'doesWin' => $this->board->doesWin(),
            'mode' => $this->getMode(),
        ];
    }

    /**
     * Returns a computer generated response to the current position.
     *
     * @param array $options
     * @param array $params
     * @return array|null
     */
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

    /**
     * Makes a move.
     *
     * @param string $color
     * @param string $pgn
     * @return bool true if the move can be made; otherwise false
     */
    public function play(string $color, string $pgn): bool
    {
        return $this->board->play($color, $pgn);
    }

    /**
     * Makes a move in long algebraic notation.
     *
     * @param string $color
     * @param string $lan
     * @return bool true if the move can be made; otherwise false
     */
    public function playLan(string $color, string $lan): bool
    {
        return $this->board->playLan($color, $lan);
    }
}
