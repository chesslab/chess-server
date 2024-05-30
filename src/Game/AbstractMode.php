<?php

namespace ChessServer\Game;

use Chess\FenToBoardFactory;
use Chess\Function\StandardFunction;
use Chess\Heuristics\FenHeuristics;
use Chess\Movetext\NagMovetext;
use Chess\Tutor\FenEvaluation;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Classical\Board as ClassicalBoard;
use ChessServer\Game\Game;
use ChessServer\Command\HeuristicsCommand;
use ChessServer\Command\LegalCommand;
use ChessServer\Command\PlayLanCommand;
use ChessServer\Command\StockfishCommand;
use ChessServer\Command\TutorFenCommand;
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
        switch (get_class($cmd)) {
            case HeuristicsCommand::class:
                $board = FenToBoardFactory::create($argv[1], new ClassicalBoard());
                return [
                    $cmd->name => [
                        'names' => (new StandardFunction())->names(),
                        'balance' => (new FenHeuristics($board))->getBalance(),
                    ],
                ];

            case LegalCommand::class:
                return [
                    $cmd->name => $this->game->getBoard()->legal($argv[1]),
                ];

            case PlayLanCommand::class:
                $isValid = $this->game->playLan($argv[1], $argv[2]);
                return [
                    $cmd->name => [
                      ... (array) $this->game->state(),
                      'variant' =>  $this->game->getVariant(),
                      'isValid' => $isValid,
                    ],
                ];

            case StockfishCommand::class:
                if (!$this->game->state()->isMate && !$this->game->state()->isStalemate) {
                    $options = json_decode(stripslashes($argv[1]), true);
                    $params = json_decode(stripslashes($argv[2]), true);
                    $computer = $this->game->computer($options, $params);
                    if ($computer->pgn) {
                        $this->game->play($this->game->state()->turn, $computer->pgn);
                    }
                }
                return [
                    $cmd->name => [
                      ... (array) $this->game->state(),
                      'variant' =>  $this->game->getVariant(),
                    ],
                ];

            case TutorFenCommand::class:
                $board = FenToBoardFactory::create($argv[1], new ClassicalBoard());
                $paragraph = (new FenEvaluation($board))->getParagraph();
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
    }
}
