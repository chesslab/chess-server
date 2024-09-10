<?php

namespace ChessServer\Command\Game\Mode;

use ChessServer\Command\Game\Game;
use ChessServer\Command\Game\LegalCommand;
use ChessServer\Command\Game\PlayLanCommand;
use ChessServer\Command\Game\StockfishCommand;
use ChessServer\Command\Game\UndoCommand;

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

    public function res($params, $cmd)
    {
        switch (get_class($cmd)) {
            case LegalCommand::class:
                return [
                    $cmd->name => $this->game->getBoard()->legal($params['square']),
                ];

            case PlayLanCommand::class:
                $isValid = $this->game->playLan($params['color'], $params['lan']);
                return [
                    $cmd->name => [
                      ... (array) $this->game->state(),
                      'variant' =>  $this->game->getVariant(),
                      'isValid' => $isValid,
                    ],
                ];

            case StockfishCommand::class:
                if (!$this->game->state()->end) {  
                    $computer = $this->game->computer($params['options'], $params['params']);
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
