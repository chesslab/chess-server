<?php

namespace ChessServer\Cli;

use ChessServer\Socket\TcpSocket;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

$server = new TcpSocket($_ENV['TCP_PORT']);
