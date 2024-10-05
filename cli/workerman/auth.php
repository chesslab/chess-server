<?php

namespace ChessServer\Cli\Workerman;

use ChessServer\Command\Parser;
use ChessServer\Command\Auth\Cli;
use ChessServer\Socket\Workerman\ClientStorage;
use ChessServer\Socket\Workerman\AuthWebSocket;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Spatie\Async\Pool;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$pool = Pool::create();

$logger = new Logger('auth');
$logger->pushHandler(new StreamHandler(AuthWebSocket::STORAGE_FOLDER . '/auth.log', Logger::INFO));

$parser = new Parser(new Cli($pool));

$clientStorage = new ClientStorage($logger);

$socketName = "websocket://{$_ENV['WSS_ADDRESS']}:{$_ENV['WSS_AUTH_PORT']}";

$context = [
    'ssl' => [
        'local_cert'  => __DIR__  . '/../../ssl/fullchain.pem',
        'local_pk' => __DIR__  . '/../../ssl/privkey.pem',
        'verify_peer' => false,
    ],
];

$webSocket = (new AuthWebSocket($socketName, $context, $parser))->init($clientStorage);

$webSocket->getWorker()->runAll();
