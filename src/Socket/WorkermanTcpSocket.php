<?php

namespace ChessServer\Socket;

use Workerman\Worker;

class WorkermanTcpSocket extends WorkermanSocket
{
    public function __construct(string $port, string $address)
    {
        parent::__construct();

        $this->worker = new Worker("tcp://$address:$port");

        $this->connect()->message()->error()->close();
    }
}
