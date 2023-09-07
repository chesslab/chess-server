<?php

namespace ChessServer\Socket;

use ChessServer\Command\LeaveCommand;
use ChessServer\GameMode\PlayMode;
use ChessServer\Exception\InternalErrorException;
use ChessServer\Exception\ParserException;
use React\Socket\ConnectionInterface;
use React\Socket\TcpServer;

class TcpSocket extends ChessSocket
{
    private TcpServer $server;

    public function __construct()
    {
        parent::__construct();

        $this->server = new TcpServer(8080);

        $this->onConnection()
            ->onError();
    }

    public function onConnection()
    {
        $this->server->on('connection', function (ConnectionInterface $conn) {
            $resourceId = get_resource_id($conn->stream);

            $this->clients[$resourceId] = $conn;

            $this->log->info('New connection', [
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
                if ($gameMode = $this->gameModeStorage->getByResourceId($resourceId)) {
                    $this->gameModeStorage->delete($gameMode);
                    $this->sendToMany(
                        $gameMode->getResourceIds(),
                        ['/leave' => LeaveCommand::ACTION_ACCEPT]
                    );
                }

                if (isset($this->clients[$resourceId])) {
                    unset($this->clients[$resourceId]);
                }

                $this->log->info('Closed connection', [
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
            $this->log->info('Occurred an error', ['message' => $e->getMessage()]);
        });

        return $this;
    }

    public function sendToOne(int $resourceId, array $res)
    {
        if (isset($this->clients[$resourceId])) {
            $this->clients[$resourceId]->write(json_encode($res));

            $this->log->info('Sent message', [
                'id' => $resourceId,
                'cmd' => array_keys($res),
            ]);
        }
    }

    public function sendToMany(array $resourceIds, array $res)
    {
        foreach ($resourceIds as $resourceId) {
            $this->clients[$resourceId]->write(json_encode($res));
        }

        $this->log->info('Sent message', [
            'ids' => $resourceIds,
            'cmd' => array_keys($res),
        ]);
    }

    public function sendToAll()
    {
        $res = [
            'broadcast' => [
                'onlineGames' => $this->gameModeStorage
                    ->decodeByPlayMode(PlayMode::STATUS_PENDING, PlayMode::SUBMODE_ONLINE),
            ],
        ];

        foreach ($this->clients as $client) {
            $client->write(json_encode($res));
        }
    }
}
