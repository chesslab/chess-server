<?php

namespace ChessServer\Cli\Workerman;

use ChessServer\Command\Parser;
use ChessServer\Socket\Workerman\ClientStorage;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$logger = new Logger('data');
$logger->pushHandler(new StreamHandler(__DIR__.'/../../storage' . '/binary.log', Logger::INFO));

echo 'TODO' . PHP_EOL;
