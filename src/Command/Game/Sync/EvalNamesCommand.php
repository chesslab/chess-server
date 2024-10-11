<?php

namespace ChessServer\Command\Game\Sync;

use Chess\Function\FastFunction;
use ChessServer\Command\AbstractSyncCommand;
use ChessServer\Socket\AbstractSocket;

class EvalNamesCommand extends AbstractSyncCommand
{
    public function __construct()
    {
        $this->name = '/eval_names';
        $this->description = 'Returns the evaluation names.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        return $socket->getClientStorage()->send([$id], [
            $this->name => (new FastFunction())->names(),
        ]);
    }
}
