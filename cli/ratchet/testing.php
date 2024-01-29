<?php

namespace ChessServer\Cli\Ratchet;

use ChessServer\Socket\TcpSocket\RatchetTcpSocket;
use Dotenv\Dotenv;

require __DIR__  . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$server = new RatchetTcpSocket($_ENV['TCP_PORT']);
