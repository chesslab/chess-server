<?php

namespace ChessServer\Cli;

use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;

require __DIR__  . '/../vendor/autoload.php';

$server = new HttpServer(function (Psr\Http\Message\ServerRequestInterface $request) {
    return Response::plaintext(
        "Hello World!\n"
    );
});

$socket = new SocketServer('127.0.0.1:8080');
$server->listen($socket);

echo "Server running at http://127.0.0.1:8080" . PHP_EOL;
