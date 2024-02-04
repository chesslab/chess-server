<?php

namespace ChessServer\Socket;

use ChessServer\Command\LeaveCommand;
use ChessServer\Exception\InternalErrorException;
use ChessServer\Exception\ParserException;
use Workerman\Worker;

class WorkermanSocket extends ChesslaBlabSocket
{
    protected Worker $worker;

    protected function connect()
    {
        $this->worker->onConnect = function($conn) {
            $conn->onWebSocketConnect = function($conn , $httpBuffer) {
                if (!str_starts_with($_SERVER['HTTP_ORIGIN'], "{$_ENV['WSS_ALLOWED_SCHEME']}://{$_ENV['WSS_ALLOWED_HOST']}")) {
                    $conn->close();
                } else {
                    $this->clientStorage->attach($conn);
                    $this->clientStorage->getLogger()->info('New connection', [
                        'id' => $conn->id,
                        'n' => $this->clientStorage->count()
                    ]);
                }
            };
        };

        return $this;
    }

    protected function message()
    {
        $this->worker->onMessage = function ($conn, $msg) {
            if (strlen($msg) > 4096) {
                return $this->clientStorage->sentToOne($conn->id, [
                    'error' => 'Internal server error',
                ]);
            }

            try {
                $cmd = $this->parser->validate($msg);
            } catch (ParserException $e) {
                return $this->clientStorage->sentToOne($conn->id, [
                    'error' => 'Command parameters not valid',
                ]);
            }

            try {
                $cmd->run($this, $this->parser->argv, $conn->id);
            } catch (InternalErrorException $e) {
                return $this->clientStorage->sentToOne($conn->id, [
                    'error' => 'Internal server error',
                ]);
            }
        };

        return $this;
    }

    protected function error()
    {
        $this->worker->onError = function ($conn, $code, $msg) {
            $conn->close();

            $this->clientStorage->getLogger()->info('Occurred an error', ['message' => $msg]);
        };

        return $this;
    }

    protected function close()
    {
        $this->worker->onClose = function ($conn) {
            if ($gameMode = $this->gameModeStorage->getById($conn->id)) {
                $this->gameModeStorage->delete($gameMode);
                $this->clientStorage->sendToMany($gameMode->getResourceIds(), [
                    '/leave' => [
                        'action' => LeaveCommand::ACTION_ACCEPT,
                    ],
                ]);
            }

            $this->clientStorage->dettachById($conn->id);

            $this->clientStorage->getLogger()->info('Closed connection', [
                'id' => $conn->id,
                'n' => $this->clientStorage->count()
            ]);
        };

        return $this;
    }

    public function run()
    {
        $this->worker->runAll();
    }
}
