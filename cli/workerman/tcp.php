<?php

namespace ChessServer\Cli\Workerman;

use ChessServer\Socket\WorkermanTcpSocket;
use Dotenv\Dotenv;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$server = new WorkermanTcpSocket($_ENV['TCP_PORT'], $_ENV['TCP_ADDRESS']);

$server->run();
