<?php

namespace ChessServer\Cli\Workerman;

use ChessServer\Socket\WebSocket\WorkermanSocket;
use Dotenv\Dotenv;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$server = new WorkermanSocket($_ENV['WS_PORT'], $_ENV['WS_ADDRESS']);

$server->run();
