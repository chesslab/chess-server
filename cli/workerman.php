<?php

namespace ChessServer\Cli;

use Dotenv\Dotenv;
use Workerman\Worker;

require __DIR__  . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

$worker = new Worker("websocket://{$_ENV['WS_ADDRESS']}:{$_ENV['WS_PORT']}");

$worker->onConnect = function ($connection) {
    echo "New connection\n";
};

$worker->onMessage = function ($connection, $data) {
    $connection->send('Hello ' . $data);
};

$worker->onClose = function ($connection) {
    echo "Connection closed\n";
};

Worker::runAll();
