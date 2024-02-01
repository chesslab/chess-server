<?php

namespace ChessServer\Cli\Ratchet;

use ChessServer\Socket\WebSocket\RatchetWebSocket;
use Dotenv\Dotenv;
use Ratchet\Http\HttpServer;
use Ratchet\Http\OriginCheck;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\LimitingServer;
use React\Socket\Server;
use React\Socket\SecureServer;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$allowed = [
    $_ENV['WSS_ALLOWED'],
];

$loop = Factory::create();

$server = new Server("$_ENV['WSS_ADDRESS']:{$_ENV['WSS_PORT']}", $loop);

$secureServer = new SecureServer($server, $loop, [
    'local_cert'  => __DIR__  . '/../../ssl/fullchain.pem',
    'local_pk' => __DIR__  . '/../../ssl/privkey.pem',
    'verify_peer' => false,
]);

$limitingServer = new LimitingServer($secureServer, 50);

$httpServer = new HttpServer(
    new OriginCheck(
      new WsServer(
          new RatchetWebSocket()
      ),
      $allowed,
    )
);

$ioServer = new IoServer($httpServer, $limitingServer, $loop);

$ioServer->run();
