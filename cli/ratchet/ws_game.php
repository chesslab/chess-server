<?php

namespace ChessServer\Cli\Ratchet;

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
use Spatie\Async\Pool;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$pool = Pool::create();

$logger = new Logger('log');
$logger->pushHandler(new StreamHandler(__DIR__.'/../../storage' . '/game.log', Logger::INFO));

$parser = new Parser(new Cli($pool));

$clientStorage = new ClientStorage($logger);

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
