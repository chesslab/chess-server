<?php

namespace ChessServer\Game;

use Chess\Computer\GrandmasterMove;
use Chess\UciEngine\UciEngine;
use Chess\UciEngine\Details\Limit;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\StartPosition as Chess960StartPosition;
use Chess\Variant\Classical\Board as ClassicalBoard;

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

    const MODE_FEN = 'fen';
    const MODE_PLAY = 'play';
    const MODE_SAN = 'san';
    const MODE_STOCKFISH = 'stockfish';

    /**
     * Chess board.
     *
     * @var \Chess\Variant\Classical\Board
     */
    private ClassicalBoard $board;

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
        }
    }

    /**
     * Returns the chess board object.
     *
     * @return \Chess\Variant\Classical\Board
     */
    public function getBoard(): ClassicalBoard
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
     * @param \Chess\Variant\Classical\Board $board
     * @return \ChessServer\Game
     */
    public function setBoard(ClassicalBoard $board): Game
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
        $history = $this->board->getHistory();
        $end = end($history);

        return (object) [
            'turn' => $this->board->getTurn(),
            'pgn' => $end ? $end->move->pgn : null,
            'castlingAbility' => $this->board->getCastlingAbility(),
            'movetext' => $this->board->getMovetext(),
            'fen' => $this->board->toFen(),
            'isCapture' => $end ? $end->move->isCapture : false,
            'isCheck' => $this->board->isCheck(),
            'isMate' => $this->board->isMate(),
            'isStalemate' => $this->board->isStalemate(),
            'isFivefoldRepetition' => $this->board->isFivefoldRepetition(),
            'isFiftyMoveDraw' => $this->board->isFiftyMoveDraw(),
            'isDeadPositionDraw' => $this->board->isDeadPositionDraw(),
            'mode' => $this->getMode(),
        ];
    }

    /**
     * Returns a computer generated response to the current position.
     *
     * @param array $options
     * @param array $params
     * @return object|null
     */
    public function computer(array $options = [], array $params = []): ?object
    {
        if ($this->gmMove) {
            if ($move = $this->gmMove->move($this->board)) {
                return $move;
            }
        }

        $limit = (new Limit())->setDepth($params['depth']);
        $stockfish = (new UciEngine('/usr/games/stockfish'))->setOption('Skill Level', $options['Skill Level']);
        $analysis = $stockfish->analysis($this->board, $limit);

        $clone = unserialize(serialize($this->board));
        $clone->playLan($this->board->getTurn(), $analysis['bestmove']);
        $history = $clone->getHistory();
        $end = end($history);

        return (object) [
            'pgn' => $end->move->pgn,
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
