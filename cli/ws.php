<?php

namespace ChessServer;

use ChessServer\WebSocket;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require __DIR__  . '/../vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WebSocket()
        )
    ),
    8080
);

$server->run();
