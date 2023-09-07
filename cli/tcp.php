<?php

namespace ChessServer\Cli;

use React\Socket\ConnectionInterface;
use React\Socket\TcpServer;

require __DIR__ . '/../vendor/autoload.php';

$server = new TcpServer(8080);

$server->on('connection', function (ConnectionInterface $connection) {
    echo 'Plaintext connection from ' . $connection->getLocalAddress() . PHP_EOL;

    $connection->write('hello there!' . PHP_EOL);

    echo get_resource_id($connection->stream);
});

$server->on('error', function (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
});

echo 'Listening on ' . $server->getAddress() . PHP_EOL;
