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

        $this->onOpen()->onError();
    }

    public function onOpen()
    {
        $this->server->on('connection', function (ConnectionInterface $conn) {
            $resourceId = get_resource_id($conn->stream);
            $this->clients[$resourceId] = $conn;
            $this->log->info('New connection', [
                'id' => $resourceId,
                'n' => count($this->clients)
            ]);
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
}
