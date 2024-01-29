<?php

namespace ChessServer\Cli;

use ChessServer\Socket\WebSocket\WorkermanSocket;

require __DIR__  . '/../vendor/autoload.php';

$webSocket = new WorkermanSocket();

$webSocket->runAll();
