<?php

namespace ChessServer\Command\Game\Blocking;

use ChessServer\Command\AbstractBlockingCommand;
use ChessServer\Socket\AbstractSocket;

class TutorGoodPgnCommand extends AbstractBlockingCommand
{
    public function __construct()
    {
        $this->name = '/tutor_good_pgn';
        $this->description = "Explains the why of a good move in terms of chess concepts.";
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $game = $socket->getGameModeStorage()->getById($id)->getGame();

        if (!isset($game->state()->end)) {
            $this->pool->add(new TutorGoodPgnTask($game->getBoard()))
                ->then(function ($result) use ($socket, $id, $game) {
                    return $socket->getClientStorage()->send([$id], [
                        $this->name => $result,
                    ]);
                });
        }
    }
}
