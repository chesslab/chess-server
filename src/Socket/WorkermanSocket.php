<?php

namespace ChessServer\Socket;

use ChessServer\Command\LeaveCommand;
use ChessServer\Exception\InternalErrorException;
use ChessServer\Exception\ParserException;

class WorkermanSocket extends ChesslaBlabSocket
{
    protected function connect()
    {
        $this->worker->onConnect = function($conn) {
            $conn->onWebSocketConnect = function($conn , $httpBuffer) {
                if (!str_starts_with($_SERVER['HTTP_ORIGIN'], "{$_ENV['WSS_ALLOWED_SCHEME']}://{$_ENV['WSS_ALLOWED_HOST']}")) {
                    $conn->close();
                } else {
                    $this->clients[$conn->id] = $conn;
                    $this->log->info('New connection', [
                        'id' => $conn->id,
                        'n' => count($this->clients)
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

    protected function error()
    {
        $this->worker->onError = function ($conn, $code, $msg) {
            $conn->close();

            $this->log->info('Occurred an error', ['message' => $msg]);
        };

        return $this;
    }

    protected function close()
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
