<?php

namespace ChessServer\Socket;

use ChessServer\Command\LeaveCommand;
use ChessServer\Game\PlayMode;
use ChessServer\Exception\InternalErrorException;
use ChessServer\Exception\ParserException;
use React\Socket\ConnectionInterface;
use React\Socket\TcpServer;

class RatchetTcpSocket extends ChesslaBlabSocket
{
    private TcpServer $server;

    public function __construct(string $port)
    {
        parent::__construct();

        $this->server = new TcpServer($port);

        $this->onConnection()
            ->onError();
    }

    public function onConnection()
    {
        $this->server->on('connection', function (ConnectionInterface $conn) {
            $resourceId = get_resource_id($conn->stream);

            $this->clients[$resourceId] = $conn;

            $this->logger->info('New connection', [
                'id' => $resourceId,
                'n' => count($this->clients)
            ]);

            $conn->on('data', function ($msg) use ($resourceId) {
                try {
                    $cmd = $this->parser->validate($msg);
                } catch (ParserException $e) {
                    return $this->sendToOne($resourceId, [
                        'error' => 'Command parameters not valid',
                    ]);
                }

                try {
                    $cmd->run($this, $this->parser->argv, $resourceId);
                } catch (InternalErrorException $e) {
                    return $this->sendToOne($resourceId, [
                        'error' => 'Internal server error',
                    ]);
                }
            });

            $conn->on('close', function () use ($conn, $resourceId) {
                if ($gameMode = $this->gameModeStorage->getById($resourceId)) {
                    $this->gameModeStorage->delete($gameMode);
                    $this->sendToMany($gameMode->getResourceIds(), [
                        '/leave' => [
                            'action' => LeaveCommand::ACTION_ACCEPT,
                        ],
                    ]);
                }

                if (isset($this->clients[$resourceId])) {
                    unset($this->clients[$resourceId]);
                }

                $this->logger->info('Closed connection', [
                    'id' => $resourceId,
                    'n' => count($this->clients)
                ]);
            });
        });

        return $this;
    }

    public function onError()
    {
        $this->server->on('error', function (Exception $e) {
            $this->logger->info('Occurred an error', ['message' => $e->getMessage()]);
        });

        return $this;
    }
}
