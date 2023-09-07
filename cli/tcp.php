<?php

namespace ChessServer\Cli;

use ChessServer\Socket\TcpSocket;

require __DIR__ . '/../vendor/autoload.php';

$server = new TcpSocket(8080);
