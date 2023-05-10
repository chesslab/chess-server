<?php

namespace ChessServer\Command;

use ChessServer\Socket;
use ChessServer\GameMode\AnalysisMode;
use ChessServer\GameMode\GmMode;
use ChessServer\GameMode\FenMode;
use ChessServer\GameMode\PgnMode;
use ChessServer\GameMode\PlayMode;
use ChessServer\GameMode\StockfishMode;
use Ratchet\ConnectionInterface;

class UndoCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/undo';
        $this->description = 'Undoes the last move.';
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(Socket $socket, array $argv, ConnectionInterface $from)
    {
        $gameMode = $socket->gameModeByResourceId($from->resourceId);

        if (is_a($gameMode, PlayMode::class)) {
            return $socket->sendToMany(
                $gameMode->getResourceIds(),
                $gameMode->res($argv, $this)
            );
        } elseif (
            is_a($gameMode, AnalysisMode::class) ||
            is_a($gameMode, FenMode::class) ||
            is_a($gameMode, GmMode::class) ||
            is_a($gameMode, PgnMode::class) ||
            is_a($gameMode, StockfishMode::class)
        ) {
            return $socket->sendToOne(
                $from->resourceId,
                $gameMode->res($argv, $this)
            );
        }
    }
}
