<?php

namespace ChessServer\Cli\Ratchet;

use ChessServer\Command\CommandParser;
use ChessServer\Command\Play\CommandContainer;
use ChessServer\Socket\RatchetClientStorage;
use ChessServer\Socket\RatchetWebSocket;
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

$logger = new Logger('log');
$logger->pushHandler(new StreamHandler(__DIR__.'/../../storage' . '/pchess.log', Logger::INFO));

$clientStorage = new RatchetClientStorage($logger);

$parser = new CommandParser(new CommandContainer());

$webSocket = (new RatchetWebSocket($parser))->init($clientStorage);

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
