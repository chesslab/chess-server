<?php

namespace ChessServer\Game;

use Chess\FenToBoard;
use Chess\Function\StandardFunction;
use Chess\Heuristics\FenHeuristics;
use Chess\Movetext\NagMovetext;
use Chess\Tutor\FenExplanation;
use Chess\UciEngine\Stockfish;
use Chess\Variant\Capablanca\Board as CapablancaBoard;
use Chess\Variant\Capablanca\FEN\StrToBoard as CapablancaFenStrToBoard;
use Chess\Variant\CapablancaFischer\Board as CapablancaFischerBoard;
use Chess\Variant\CapablancaFischer\FEN\StrToBoard as CapablancaFischerStrToBoard;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\FEN\StrToBoard as Chess960FenStrToBoard;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Classical\FEN\StrToBoard as ClassicalFenStrToBoard;
use ChessServer\Game\Game;
use ChessServer\Command\HeuristicsCommand;
use ChessServer\Command\LegalCommand;
use ChessServer\Command\PlayLanCommand;
use ChessServer\Command\StockfishCommand;
use ChessServer\Command\StockfishEvalCommand;
use ChessServer\Command\TutorFenCommand;
use ChessServer\Command\UndoCommand;
use ChessServer\Exception\InternalErrorException;

abstract class AbstractMode
{
    protected $game;

    protected $resourceIds;

    protected $hash;

    public function __construct(Game $game, array $resourceIds)
    {
        $this->game = $game;
        $this->resourceIds = $resourceIds;
    }

    public function getGame()
    {
        return $this->game;
    }

    public function setGame(Game $game)
    {
        $this->game = $game;

        return $this;
    }

    public function getResourceIds(): array
    {
        return $this->resourceIds;
    }

    public function setResourceIds(array $resourceIds)
    {
        $this->resourceIds = $resourceIds;

        return $this;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function res($argv, $cmd)
    {
        try {
            switch (get_class($cmd)) {
                case HeuristicsCommand::class:
                    return [
                        $cmd->name => [
                            'names' => (new StandardFunction())->names(),
                            'balance' => (new FenHeuristics($argv[1], $argv[2]))
                                ->getBalance(),
                        ],
                    ];
                case LegalCommand::class:
                    return [
                        $cmd->name => $this->game->getBoard()->legal($argv[1]),
                    ];
                case PlayLanCommand::class:
                    $this->game->playLan($argv[1], $argv[2]);
                    return [
                        $cmd->name => [
                          ... (array) $this->game->state(),
                          'variant' =>  $this->game->getVariant(),
                        ],
                    ];
                case StockfishCommand::class:
                    if (!$this->game->state()->isMate && !$this->game->state()->isStalemate) {
                        $options = json_decode(stripslashes($argv[1]), true);
                        $params = json_decode(stripslashes($argv[2]), true);
                        $ai = $this->game->ai($options, $params);
                        if ($ai->move) {
                            $this->game->play($this->game->state()->turn, $ai->move);
                        }
                    }
                    return [
                        $cmd->name => [
                          ... (array) $this->game->state(),
                          'variant' =>  $this->game->getVariant(),
                        ],
                    ];
                case StockfishEvalCommand::class:
                    $board = FenToBoard::create($argv[1]);
                    $stockfish = new Stockfish($board);
                    $nag = $stockfish->evalNag($board->toFen(), 'Final');
                    return [
                        $cmd->name => NagMovetext::glyph($nag),
                    ];
                case TutorFenCommand::class:
                    if ($argv[1] === Chess960Board::VARIANT) {
                        $startPos = str_split($argv[3]);
                        $board = (new Chess960FenStrToBoard($argv[2], $startPos))->create();
                    } elseif ($argv[1] === CapablancaBoard::VARIANT) {
                        $board = (new CapablancaFenStrToBoard($argv[2]))->create();
                    } elseif ($argv[1] === CapablancaFischerBoard::VARIANT) {
                        $startPos = str_split($argv[3]);
                        $board = (new CapablancaFischerStrToBoard($argv[2], $startPos))->create();
                    } elseif ($argv[1] === ClassicalBoard::VARIANT) {
                        $board = (new ClassicalFenStrToBoard($argv[2]))->create();
                    }
                    $paragraph = (new FenExplanation($board))->getParagraph();
                    return [
                        $cmd->name => implode(' ', $paragraph),
                    ];
                case UndoCommand::class:
                    $board = $this->game->getBoard()->undo();
                    $this->game->setBoard($board);
                    return [
                        $cmd->name => [
                          ... (array) $this->game->state(),
                          'variant' =>  $this->game->getVariant(),
                        ],
                    ];
                default:
                    return null;
            }
        } catch (\Exception $e) {
            throw new InternalErrorException();
        }
    }
}
