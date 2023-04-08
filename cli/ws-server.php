<?php

namespace ChessServer;

use ChessServer\Socket;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require __DIR__  . '/../vendor/autoload.php';

$socket = new Socket();

$server = IoServer::factory(
    new HttpServer(
        new WsServer($socket)
    ),
    8080
);

$server->loop->addPeriodicTimer(3, function () use ($socket) {
    $socket->broadcast();
});

$server->run();
