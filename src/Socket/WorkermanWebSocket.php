<?php

namespace ChessServer\Socket;

use Workerman\Worker;

class WorkermanWebSocket extends WorkermanSocket
{
    public function __construct(string $socketName, array $context)
    {
        parent::__construct();

        $this->worker = new Worker($socketName, $context);
        $this->worker->transport = 'ssl';

        $this->connect()->message()->error()->close();
    }
}
