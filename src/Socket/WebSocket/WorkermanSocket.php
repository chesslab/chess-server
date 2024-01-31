<?php

namespace ChessServer\Socket\WebSocket;

use ChessServer\Exception\InternalErrorException;
use ChessServer\Exception\ParserException;
use ChessServer\Socket\ChesslaBlab;
use ChessServer\Socket\SendInterface;
use Workerman\Worker;

class WorkermanSocket extends ChesslaBlab implements SendInterface
{
    private Worker $worker;

    public function __construct(string $port, string $address)
    {
        parent::__construct();

        $this->worker = new Worker("websocket://$address:$port");

        $this->connect()->message()->close();
    }

    private function connect()
    {
        $this->worker->onConnect = function ($conn) {
            $this->clients[$conn->id] = $conn;

            $this->log->info('New connection', [
                'id' => $conn->id,
                'n' => count($this->clients)
            ]);
        };

        return $this;
    }

    private function message()
    {
        $this->worker->onMessage = function ($conn, $msg) {
            if (strlen($msg) > 4096) {
                return $this->sendToOne($conn->id, [
                    'error' => 'Internal server error',
                ]);
            }

            try {
                $cmd = $this->parser->validate($msg);
            } catch (ParserException $e) {
                return $this->sendToOne($conn->id, [
                    'error' => 'Command parameters not valid',
                ]);
            }

            try {
                $cmd->run($this, $this->parser->argv, $conn->id);
            } catch (InternalErrorException $e) {
                return $this->sendToOne($conn->id, [
                    'error' => 'Internal server error',
                ]);
            }
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
        if (isset($this->clients[$resourceId])) {
            $this->clients[$resourceId]->send(json_encode($res));

            $this->log->info('Sent message', [
                'id' => $resourceId,
                'cmd' => array_keys($res),
            ]);
        }
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
