<?php

namespace ChessServer\Socket;

use ChessServer\Command\LeaveCommand;
use ChessServer\Game\PlayMode;
use ChessServer\Exception\InternalErrorException;
use ChessServer\Exception\ParserException;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class RatchetWebSocket extends ChesslaBlabSocket implements MessageComponentInterface
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
}
