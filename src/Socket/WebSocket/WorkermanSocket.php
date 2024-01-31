<?php

namespace ChessServer\Socket\WebSocket;

use ChessServer\Socket\ChesslaBlab;
use Workerman\Worker;

class WorkermanSocket extends ChesslaBlab
{
    private Worker $worker;

    public function __construct(string $port, string $address)
    {
        $this->worker = new Worker("websocket://$address:$port");

        $this->connect()->message()->close();
    }

    public function connect()
    {
        $this->worker->onConnect = function ($connection) {
            echo "New connection\n";
        };

        return $this;
    }

    public function message()
    {
        $this->worker->onMessage = function ($connection, $data) {
            $connection->send('Hello ' . $data);
        };

        return $this;
    }

    public function close()
    {
        $this->worker->onClose = function ($connection) {
            echo "Connection closed\n";
        };

        return $this;
    }

    public function run()
    {
        $this->worker->runAll();
    }
}
