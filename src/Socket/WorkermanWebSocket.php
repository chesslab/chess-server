<?php

namespace ChessServer\Socket;

use ChessServer\Command\CommandParser;
use ChessServer\Command\Game\LeaveCommand;
use ChessServer\Exception\ParserException;
use Workerman\Worker;

class WorkermanWebSocket extends ChesslaBlabSocket
{
    private Worker $worker;

    public function __construct(string $socketName, array $context, CommandParser $parser)
    {
        parent::__construct($parser);

        $this->worker = new Worker($socketName, $context);
        $this->worker->transport = 'ssl';

        $this->connect()->message()->error()->close();
    }

    public function getWorker()
    {
        return $this->worker;
    }

    protected function connect()
    {
        $this->worker->onConnect = function($conn) {
            $conn->onWebSocketConnect = function($conn , $httpBuffer) {
                $this->clientStorage->attach($conn);
                $this->clientStorage->getLogger()->info('New connection', [
                    'id' => $conn->id,
                    'n' => $this->clientStorage->count()
                ]);
            };
        };

        return $this;
    }

    protected function message()
    {
        $this->worker->onMessage = function ($conn, $msg) {
            if (strlen($msg) > 4096) {
                return $this->clientStorage->sendToOne($conn->id, [
                    'error' => 'Internal server error',
                ]);
            }

            try {
                $cmd = $this->parser->validate($msg);
            } catch (ParserException $e) {
                return $this->clientStorage->sendToOne($conn->id, [
                    'error' => 'Command parameters not valid',
                ]);
            }

            try {
                $cmd->run($this, $this->parser->argv, $conn->id);
            } catch (\Throwable $e) {
                $this->clientStorage->getLogger()->error('Occurred an error', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return $this->clientStorage->sendToOne($conn->id, [
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

            $this->clientStorage->getLogger()->error('Occurred an error', ['message' => $msg]);
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

            $this->clientStorage->detachById($conn->id);

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
