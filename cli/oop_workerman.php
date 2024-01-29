<?php

namespace ChessServer\Cli;

use ChessServer\Socket\Workerman\WebSocket;

require __DIR__  . '/../vendor/autoload.php';

$webSocket = new WebSocket();

$webSocket->runAll();
