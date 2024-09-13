<?php

namespace ChessServer\Cli\Ratchet;

use ChessServer\Db;
use ChessServer\Command\Parser;
use ChessServer\Command\Game\Cli;
use ChessServer\Socket\Ratchet\ClientStorage;
use ChessServer\Socket\Ratchet\GameWebSocket;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ratchet\Http\HttpServer;
use Ratchet\Http\OriginCheck;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$db = new Db([
   'driver' => $_ENV['DB_DRIVER'],
   'host' => $_ENV['DB_HOST'],
   'database' => $_ENV['DB_DATABASE'],
   'username' => $_ENV['DB_USERNAME'],
   'password' => $_ENV['DB_PASSWORD'],
]);

$logger = new Logger('log');
$logger->pushHandler(new StreamHandler(__DIR__.'/../../storage' . '/game.log', Logger::INFO));

$clientStorage = new ClientStorage($logger);

$parser = new Parser(new Cli($db));

$webSocket = (new GameWebSocket($parser))->init($clientStorage);

$ioServer = IoServer::factory(
    new HttpServer(
        new WsServer(
            $webSocket
        )
    ),
    $_ENV['WS_GAME_PORT'],
    $_ENV['WS_ADDRESS']
);

$ioServer->run();
