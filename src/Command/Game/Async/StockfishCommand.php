<?php

namespace ChessServer\Command\Game\Async;

use ChessServer\Command\AbstractAsyncCommand;
use ChessServer\Socket\AbstractSocket;

class StockfishCommand extends AbstractAsyncCommand
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
        $game = $socket->getGameModeStorage()->getById($id)->getGame();

        if (!isset($game->state()->end)) {
            $this->pool->add(new StockfishTask($params, $game->getBoard()))
                ->then(function ($result) use ($socket, $id, $game) {
                    if ($result['pgn']) {
                        $game->play($game->state()->turn, $result['pgn']);
                    }
                    return $socket->getClientStorage()->send([$id], [
                        $this->name => [
                            ...(array) $game->state(),
                            'variant' => $game->getVariant(),
                        ],
                    ]);
                });
        }
    }
}
