<?php

namespace ChessServer\Cli\Ratchet;

use ChessServer\Command\Parser;
use ChessServer\Command\Binary\Cli;
use ChessServer\Socket\Ratchet\ClientStorage;
use ChessServer\Socket\Ratchet\BinaryWebSocket;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ratchet\Http\HttpServer;
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
$logger->pushHandler(new StreamHandler(__DIR__.'/../../storage' . '/binary.log', Logger::INFO));

$clientStorage = new ClientStorage($logger);

$parser = new Parser(new Cli());

$webSocket = (new BinaryWebSocket($parser))->init($clientStorage);

$server = new Server("{$_ENV['WSS_ADDRESS']}:{$_ENV['WSS_BINARY_PORT']}", $webSocket->getLoop());

$secureServer = new SecureServer($server, $webSocket->getLoop(), [
    'local_cert'  => __DIR__  . '/../../ssl/fullchain.pem',
    'local_pk' => __DIR__  . '/../../ssl/privkey.pem',
    'verify_peer' => false,
]);

$limitingServer = new LimitingServer($secureServer, 50);

$httpServer = new HttpServer(new WsServer($webSocket));

$ioServer = new IoServer($httpServer, $limitingServer, $webSocket->getLoop());

$ioServer->run();
