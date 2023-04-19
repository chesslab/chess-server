<?php

namespace ChessServer;

use ChessServer\Socket;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\LimitingServer;
use React\Socket\Server;
use React\Socket\SecureServer;

require __DIR__  . '/../vendor/autoload.php';

$loop = Factory::create();

$server = new Server('0.0.0.0:8443', $loop);

$secureServer = new SecureServer($server, $loop, [
    'local_cert'  => __DIR__  . '/../ssl/certificate.crt',
    'local_pk' => __DIR__  . '/../ssl/private.key',
    'verify_peer' => false,
]);

$limitingServer = new LimitingServer($secureServer, 50);

$httpServer = new HttpServer(
    new WsServer(
        new Socket()
    )
);

$ioServer = new IoServer($httpServer, $limitingServer, $loop);

$ioServer->run();
