<?php

namespace ChessServer\Socket\WebSocket;

use ChessServer\Command\LeaveCommand;
use ChessServer\Exception\InternalErrorException;
use ChessServer\Exception\ParserException;
use ChessServer\Socket\ChesslaBlab;
use Workerman\Worker;

class WorkermanSocket extends ChesslaBlab
{
    private Worker $worker;

    public function __construct(string $port, string $address, array $context = [])
    {
        parent::__construct();

        if (isset($context['ssl'])) {
            $this->worker = new Worker("websocket://$address:$port", $context);
            $this->worker->transport = 'ssl';
        } else {
            $this->worker = new Worker("websocket://$address:$port");
        }

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
        $this->worker->onClose = function ($conn) {
            if ($gameMode = $this->gameModeStorage->getByResourceId($conn->id)) {
                $this->gameModeStorage->delete($gameMode);
                $this->sendToMany($gameMode->getResourceIds(), [
                    '/leave' => [
                        'action' => LeaveCommand::ACTION_ACCEPT,
                    ],
                ]);
            }

            if (isset($this->clients[$conn->id])) {
                unset($this->clients[$conn->id]);
            }

            $this->log->info('Closed connection', [
                'id' => $conn->id,
                'n' => count($this->clients)
            ]);
        };

        return $this;
    }

    public function run()
    {
        $this->worker->runAll();
    }
}
