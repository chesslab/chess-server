<?php

namespace ChessServer\Socket\WebSocket;

use ChessServer\Socket\ChesslaBlab;
use ChessServer\Socket\SendInterface;
use Workerman\Worker;

class WorkermanSocket extends ChesslaBlab implements SendInterface
{
    private Worker $worker;

    public function __construct(string $port, string $address)
    {
        $this->worker = new Worker("websocket://$address:$port");

        $this->connect()->message()->close();
    }

    private function connect()
    {
        $this->worker->onConnect = function ($connection) {
            echo "New connection\n";
        };

        return $this;
    }

    private function message()
    {
        $this->worker->onMessage = function ($connection, $data) {
            $connection->send('Hello ' . $data);
        };

        return $this;
    }

    private function close()
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

    public function sendToOne(int $resourceId, array $res): void
    {
        // TODO
    }

    public function sendToMany(array $resourceIds, array $res): void
    {
        // TODO
    }

    public function sendToAll(): void
    {
        // TODO
    }
}
