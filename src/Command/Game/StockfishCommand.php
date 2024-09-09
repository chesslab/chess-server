<?php

namespace ChessServer\Command\Game;

use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\AbstractSocket;

class StockfishCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/stockfish';
        $this->description = "Returns Stockfish's response to the current position.";
        $this->params = [
            'settings' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $settings = json_decode(stripslashes($argv[1]), true);

        $gameMode = $socket->getGameModeStorage()->getById($id);

        return $socket->getClientStorage()->sendToOne(
            $id,
            $gameMode->res($settings, $this)
        );
    }
}
