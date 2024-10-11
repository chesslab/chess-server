<?php

namespace ChessServer\Command\Game\Async;

use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\AbstractSocket;

class PlayRavCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/play_rav';
        $this->description = 'Plays the moves in a RAV movetext.';
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

        $this->pool->add(new PlayRavTask($params), 81920)
            ->then(function ($result) use ($socket, $id) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => $result,
                ]);
            });
    }
}
