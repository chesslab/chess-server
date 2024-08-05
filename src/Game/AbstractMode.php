<?php

namespace ChessServer\Game;

use ChessServer\Game\Game;
use ChessServer\Command\Play\LegalCommand;
use ChessServer\Command\Play\PlayLanCommand;
use ChessServer\Command\Play\StockfishCommand;
use ChessServer\Command\Play\UndoCommand;

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
                    if ($computer['pgn']) {
                        $this->game->play($this->game->state()->turn, $computer['pgn']);
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
    }
}
