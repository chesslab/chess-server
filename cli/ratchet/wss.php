<?php

namespace ChessServer\Cli\Ratchet;

use ChessServer\Game\GameModeStorage;
use ChessServer\Socket\RatchetClientStorage;
use ChessServer\Socket\RatchetWebSocket;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
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

$logger = new Logger('log');
$logger->pushHandler(new StreamHandler(__DIR__.'/../../storage' . '/pchess.log', Logger::INFO));

$clientStorage = new RatchetClientStorage(new GameModeStorage(), $logger);

$webSocket = (new RatchetWebSocket())->init($clientStorage);

$allowed = [
    $_ENV['WSS_ALLOWED_HOST'],
];

$loop = Factory::create();

$server = new Server("{$_ENV['WSS_ADDRESS']}:{$_ENV['WSS_PORT']}", $loop);

$secureServer = new SecureServer($server, $loop, [
    'local_cert'  => __DIR__  . '/../../ssl/fullchain.pem',
    'local_pk' => __DIR__  . '/../../ssl/privkey.pem',
    'verify_peer' => false,
]);

$limitingServer = new LimitingServer($secureServer, 50);

$httpServer = new HttpServer(
    new OriginCheck(
      new WsServer($webSocket),
      $allowed,
    )
);

$ioServer = new IoServer($httpServer, $limitingServer, $loop);

$ioServer->run();
