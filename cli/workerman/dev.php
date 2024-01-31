<?php

use Workerman\Worker;

require __DIR__  . '/../../vendor/autoload.php';

$worker = new Worker('websocket://0.0.0.0:2346');

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
