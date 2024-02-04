<?php

namespace ChessServer\Socket;

use Workerman\Worker;

class WorkermanTcpSocket extends WorkermanSocket
{
    public function __construct(string $socketName)
    {
        parent::__construct();

        $this->worker = new Worker($socketName);

        $this->connect()->message()->error()->close();
    }
}
