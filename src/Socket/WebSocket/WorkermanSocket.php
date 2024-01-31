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
    }

    public function onConnect($connection)
    {
        $this->worker->onConnect = function ($connection) {
            echo "New connection\n";
        };
    }

    public function onMessage($connection, $data)
    {
        $this->worker->onMessage = function ($connection, $data) {
            $connection->send('Hello ' . $data);
        };
    }

    public function onClose($connection)
    {
        $this->worker->onClose = function ($connection) {
            echo "Connection closed\n";
        };
    }

    public function runAll()
    {
        $this->worker->runAll();
    }
}
