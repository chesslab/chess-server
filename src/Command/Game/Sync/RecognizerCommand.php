<?php

namespace AbstractSyncCommandChessServer\Command\Game\Sync;

use ChessServer\Command\AbstractSyncCommand;
use ChessServer\Socket\AbstractSocket;

class RecognizerCommand extends AbstractSyncCommand
{
    public function __construct()
    {
        $this->name = '/recognizer';
        $this->description = 'Returns the piece placement in FEN format of a Base64 encoded image.';
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

        $this->pool->add(new RecognizerTask($params))
            ->then(function ($result) use ($socket, $id) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => $result,
                ]);
            });
    }
}
