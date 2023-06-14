<?php

namespace ChessServer\GameMode;

use Chess\Heuristics;
use Chess\HeuristicsByFenString;
use Chess\Variant\Capablanca\Board as CapablancaBoard;
use Chess\Variant\Capablanca100\Board as Capablanca100Board;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Classical\Board as ClassicalBoard;
use ChessServer\Game;
use ChessServer\Command\HeuristicsCommand;
use ChessServer\Command\HeuristicsBarCommand;
use ChessServer\Command\LegalCommand;
use ChessServer\Command\PlayLanCommand;
use ChessServer\Command\StockfishCommand;
use ChessServer\Command\UndoCommand;

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
                    $variant = $this->game->getVariant();
                    $movetext = $argv[1];
                    if ($variant === Game::VARIANT_960) {
                        $startPos = $this->game->getBoard()->getStartPos();
                        $board = new Chess960Board($startPos);
                    } elseif ($variant === Game::VARIANT_CAPABLANCA_80) {
                        $board = new CapablancaBoard();
                    } elseif ($variant === Game::VARIANT_CAPABLANCA_100) {
                        $board = new Capablanca100Board();
                    } elseif ($variant === Game::VARIANT_CLASSICAL) {
                        $board = new ClassicalBoard();
                    }
                    return [
                        $cmd->name => [
                            'dimensions' => (new Heuristics())->getDimsNames(),
                            'balance' => (new Heuristics($movetext, $board))->getBalance(),
                        ],
                    ];
                case HeuristicsBarCommand::class:
                    $balance = (new HeuristicsByFenString($argv[1], $argv[2]))->getBalance();
                    return [
                        $cmd->name => [
                            'dimensions' => (new Heuristics())->getDimsNames(),
                            'balance' => $balance,
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
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
