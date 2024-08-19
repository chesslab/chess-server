<?php

namespace ChessServer\Cli\Workerman;

use ChessServer\Command\CommandParser;
use ChessServer\Command\Data\Cli;
use ChessServer\Command\Data\Db;
use ChessServer\Socket\Workerman\ClientStorage;
use ChessServer\Socket\Workerman\DataWebSocket;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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

$logger = new Logger('data');
$logger->pushHandler(new StreamHandler(__DIR__.'/../../storage' . '/data.log', Logger::INFO));

$parser = new CommandParser(new Cli($db));

$clientStorage = new ClientStorage($logger);

$socketName = "websocket://{$_ENV['WSS_ADDRESS']}:{$_ENV['WSS_DATA_PORT']}";

$context = [
    'ssl' => [
        'local_cert'  => __DIR__  . '/../../ssl/fullchain.pem',
        'local_pk' => __DIR__  . '/../../ssl/privkey.pem',
        'verify_peer' => false,
    ],
];

$webSocket = (new DataWebSocket($socketName, $context, $parser))->init($clientStorage);

$webSocket->getWorker()->runAll();
