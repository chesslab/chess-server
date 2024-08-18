<?php

namespace ChessServer\Cli\Ratchet;

use ChessServer\Command\CommandParser;
use ChessServer\Command\Game\CommandContainer;
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

$logger = new Logger('log');
$logger->pushHandler(new StreamHandler(__DIR__.'/../../storage' . '/game.log', Logger::INFO));

$clientStorage = new ClientStorage($logger);

$parser = new CommandParser(new CommandContainer($logger));

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
