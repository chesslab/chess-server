<?php

namespace ChessServer\Cli\Workerman;

use ChessServer\Command\Parser;
use ChessServer\Command\Binary\Cli;
use ChessServer\Socket\Workerman\ClientStorage;
use ChessServer\Socket\Workerman\BinaryWebSocket;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Spatie\Async\Pool;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$pool = Pool::create();

$logger = new Logger('binary');
$logger->pushHandler(new StreamHandler(BinaryWebSocket::STORAGE_FOLDER . '/binary.log', Logger::INFO));

$parser = new Parser(new Cli($pool));

$clientStorage = new ClientStorage($logger);

$socketName = "websocket://{$_ENV['WSS_ADDRESS']}:{$_ENV['WSS_BINARY_PORT']}";

$context = [
    'ssl' => [
        'local_cert'  => __DIR__  . '/../../ssl/fullchain.pem',
        'local_pk' => __DIR__  . '/../../ssl/privkey.pem',
        'verify_peer' => false,
    ],
];

$webSocket = (new BinaryWebSocket($socketName, $context, $parser))->init($clientStorage);

$webSocket->getWorker()->runAll();
