<?php

namespace ChessServer\Socket;

use ChessServer\Command\LeaveCommand;
use ChessServer\Game\PlayMode;
use ChessServer\Exception\InternalErrorException;
use ChessServer\Exception\ParserException;
use ChessServer\Socket\ChesslaBlab;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class RatchetWebSocket extends ChesslaBlab implements MessageComponentInterface
{
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = $conn;

        $this->log->info('New connection', [
            'id' => $conn->resourceId,
            'n' => count($this->clients)
        ]);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if (strlen($msg) > 4096) {
            return $this->sendToOne($from->resourceId, [
                'error' => 'Internal server error',
            ]);
        }

        try {
            $cmd = $this->parser->validate($msg);
        } catch (ParserException $e) {
            return $this->sendToOne($from->resourceId, [
                'error' => 'Command parameters not valid',
            ]);
        }

        try {
            $cmd->run($this, $this->parser->argv, $from->resourceId);
        } catch (InternalErrorException $e) {
            return $this->sendToOne($from->resourceId, [
                'error' => 'Internal server error',
            ]);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        if ($gameMode = $this->gameModeStorage->getByResourceId($conn->resourceId)) {
            $this->gameModeStorage->delete($gameMode);
            $this->sendToMany($gameMode->getResourceIds(), [
                '/leave' => [
                    'action' => LeaveCommand::ACTION_ACCEPT,
                ],
            ]);
        }

        if (isset($this->clients[$conn->resourceId])) {
            unset($this->clients[$conn->resourceId]);
        }

        $this->log->info('Closed connection', [
            'id' => $conn->resourceId,
            'n' => count($this->clients)
        ]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();

        $this->log->info('Occurred an error', ['message' => $e->getMessage()]);
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
        foreach ($resourceIds as $resourceId) {
            $this->clients[$resourceId]->send(json_encode($res));
        }

        $this->log->info('Sent message', [
            'ids' => $resourceIds,
            'cmd' => array_keys($res),
        ]);
    }

    public function sendToAll(): void
    {
        $res = [
            'broadcast' => [
                'onlineGames' => $this->gameModeStorage
                    ->decodeByPlayMode(PlayMode::STATUS_PENDING, PlayMode::SUBMODE_ONLINE),
            ],
        ];

        foreach ($this->clients as $client) {
            $client->send(json_encode($res));
        }
    }
}
