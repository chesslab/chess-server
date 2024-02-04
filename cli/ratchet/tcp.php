<?php

namespace ChessServer\Cli\Ratchet;

use ChessServer\Game\GameModeStorage;
use ChessServer\Socket\RatchetClientStorage;
use ChessServer\Socket\RatchetTcpSocket;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$logger = new Logger('log');
$logger->pushHandler(new StreamHandler(__DIR__.'/../../storage' . '/pchess.log', Logger::INFO));

$clientStorage = new RatchetClientStorage(new GameModeStorage(), $logger);

$server = (new RatchetTcpSocket($_ENV['TCP_PORT']))->init($clientStorage);
