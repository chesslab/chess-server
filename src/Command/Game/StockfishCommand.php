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
            'params' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $params = json_decode(stripslashes($argv[1]), true);
        $gameMode = $socket->getGameModeStorage()->getById($id);

        $this->pool->add(new StockfishAsyncTask($params, $gameMode, $this))
            ->then(function ($result) use ($socket, $id) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => $result,
                ]);
            });

        // $this->pool->wait();
    }
}
