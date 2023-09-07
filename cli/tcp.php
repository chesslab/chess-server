<?php

namespace ChessServer\Cli;

use React\Socket\ConnectionInterface;
use React\Socket\TcpServer;

require __DIR__ . '/../vendor/autoload.php';

$server = new TcpServer(8080);

$server->on('connection', function (ConnectionInterface $connection) {
    echo 'Plaintext connection from ' . $connection->getRemoteAddress() . PHP_EOL;

    $connection->write('hello there!' . PHP_EOL);
});

$server->on('error', function (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
});

echo 'Listening on ' . $server->getAddress() . PHP_EOL;
