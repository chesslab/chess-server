<?php

namespace ChessServer\Cli\Workerman;

use ChessServer\Socket\WebSocket\WorkermanSocket;
use Dotenv\Dotenv;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$context = [
    'ssl' => [
        'local_cert'  => __DIR__  . '/../../ssl/fullchain.pem',
        'local_pk' => __DIR__  . '/../../ssl/privkey.pem',
        'verify_peer' => false,
    ],
];

$server = new WorkermanSocket($_ENV['WSS_PORT'], $_ENV['WS_ADDRESS'], $context);

$server->run();
