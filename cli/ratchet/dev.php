<?php

namespace ChessServer\Cli\Ratchet;

use ChessServer\Socket\WebSocket\RatchetWebSocket;
use Dotenv\Dotenv;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new RatchetWebSocket()
        )
    ),
    $_ENV['WS_PORT'],
    $_ENV['WS_ADDRESS']
);

$server->run();
