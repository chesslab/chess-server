<?php

namespace ChessServer\Cli\Workerman;

use ChessServer\Game\GameModeStorage;
use ChessServer\Socket\WorkermanClientStorage;
use ChessServer\Socket\WorkermanTcpSocket;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$logger = new Logger('log');
$logger->pushHandler(new StreamHandler(__DIR__.'/../../storage' . '/pchess.log', Logger::INFO));

$clientStorage = new WorkermanClientStorage(new GameModeStorage(), $logger);

$socketName = "tcp://{$_ENV['TCP_ADDRESS']}:{$_ENV['TCP_PORT']}";

$server = (new WorkermanTcpSocket($socketName))->init($clientStorage);

$server->run();
