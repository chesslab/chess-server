<?php

namespace ChessServer\Socket;

use Monolog\Logger;

interface ClientStorageInterface
{
    public function getLogger(): Logger;

    public function detachById(int $id): void;

    public function send(array $ids, array $res): void;

    public function broadcast(array $res): void;
}
