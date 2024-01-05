<?php

namespace ChessServer\Game;

use Chess\FenToBoard;
use Chess\Function\StandardFunction;
use Chess\Heuristics\FenHeuristics;
use Chess\Movetext\NagMovetext;
use Chess\UciEngine\Stockfish;
use ChessServer\Game\Game;
use ChessServer\Command\HeuristicsCommand;
use ChessServer\Command\LegalCommand;
use ChessServer\Command\PlayLanCommand;
use ChessServer\Command\StockfishCommand;
use ChessServer\Command\StockfishEvalCommand;
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
