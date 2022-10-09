<?php

namespace ChessServer\GameMode;

use Chess\Game;
use Chess\Heuristics;
use Chess\HeuristicsByFenString;
use Chess\Variant\Capablanca80\Board as Capablanca80Board;
use Chess\Variant\Capablanca100\Board as Capablanca100Board;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Classical\Board as ClassicalBoard;
use ChessServer\Command\HeuristicsCommand;
use ChessServer\Command\HeuristicsBarCommand;
use ChessServer\Command\LegalSqsCommand;
use ChessServer\Command\PlayFenCommand;
use ChessServer\Command\GrandmasterCommand;
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
                case GrandmasterCommand::class:
                    $ai = $this->game->ai();
                    if ($ai) {
                        $this->game->play($this->game->state()->turn, $ai->move);
                        $game = (array) $ai->game;
                        unset($game['movetext']);
                        return [
                            $cmd->name => [
                                'game' => (object) $game,
                                'move' => $ai->move,
                                'state' => $this->game->state(),
                            ],
                        ];
                    }
                    return [
                        $cmd->name => null,
                    ];
                case HeuristicsCommand::class:
                    $variant = $this->game->getVariant();
                    $movetext = $this->game->getBoard()->getMovetext();
                    if ($variant === self::VARIANT_960) {
                        $startPos = $this->game->getBoard()->getStartPos();
                        $board = new Chess960Board($startPos);
                    } elseif ($variant === self::VARIANT_CAPABLANCA_80) {
                        $board = new Capablanca80Board();
                    } elseif ($variant === self::VARIANT_CAPABLANCA_100) {
                        $board = new Capablanca100Board();
                    } elseif ($variant === self::VARIANT_CLASSICAL) {
                        $board = new ClassicalBoard();
                    }
                    return [
                        $cmd->name => [
                            'dimensions' => (new Heuristics())->getDimsNames(),
                            'balance' => (new Heuristics($movetext, $board))->getBalance(),
                        ],
                    ];
                case HeuristicsBarCommand::class:
                    $balance = (new HeuristicsByFenString($argv[1]))->getBalance();
                    return [
                        $cmd->name => [
                            'dimensions' => (new Heuristics())->getDimsNames(),
                            'balance' => $balance,
                        ],
                    ];
                case LegalSqsCommand::class:
                    return [
                        $cmd->name => $this->game->getBoard()->legalSqs($argv[1]),
                    ];
                case PlayFenCommand::class:
                    return [
                        $cmd->name => [
                            'turn' => $this->game->state()->turn,
                            'isLegal' => $this->game->playFen($argv[1]),
                            'isCheck' => $this->game->state()->isCheck,
                            'isMate' => $this->game->state()->isMate,
                            'movetext' => $this->game->state()->movetext,
                            'fen' => $this->game->state()->fen,
                            'pgn' => $this->game->state()->pgn
                        ],
                    ];
                case StockfishCommand::class:
                    $options = json_decode(stripslashes($argv[1]), true);
                    $params = json_decode(stripslashes($argv[2]), true);
                    $ai = $this->game->ai($options, $params);
                    if ($ai->move) {
                        $this->game->play($this->game->state()->turn, $ai->move);
                    }
                    return [
                        $cmd->name => [
                            'move' => $ai->move,
                            'state' => $this->game->state(),
                        ],
                    ];
                case UndoCommand::class:
                    $board = $this->game->getBoard();
                    if ($board->getHistory()) {
                        $this->game->setBoard($board->undo());
                    }
                    return [
                        $cmd->name => $this->game->state(),
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
