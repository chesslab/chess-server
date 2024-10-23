<?php

namespace ChessServer\Socket\Workerman;

use ChessServer\Command\Parser;
use ChessServer\Exception\ParserException;
use ChessServer\Socket\AbstractSocket;
use Workerman\Worker;

abstract class AbstractWebSocket extends AbstractSocket
{
    protected Worker $worker;

    public function __construct(string $socketName, array $context, Parser $parser)
    {
        parent::__construct($parser);

        $this->worker = new Worker($socketName, $context);
        $this->worker->transport = 'ssl';
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
                    'n' => $this->clientStorage->count(),
                ]);
            };
        };

        return $this;
    }

    protected function message()
    {
        $this->worker->onMessage = function ($conn, $msg) {
            if (strlen($msg) > 128000) {
                return $this->clientStorage->send([$conn->id], [
                    'error' => 'Internal server error',
                ]);
            }

            try {
                $cmd = $this->parser->validate($msg);
            } catch (ParserException $e) {
                return $this->clientStorage->send([$conn->id], [
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
                return $this->clientStorage->send([$conn->id], [
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
            $this->clientStorage->getLogger()->error('Occurred an error', [
                'message' => $msg,
            ]);
        };

        return $this;
    }
}
