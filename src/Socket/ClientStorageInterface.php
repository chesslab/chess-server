<?php

namespace ChessServer\Socket;

use Monolog\Logger;

interface ClientStorageInterface
{
    public function getLogger(): Logger;

    public function detachById(int $id): void;

    public function sendToOne(int $id, array|string $res): void;

    public function sendToMany(array $ids, array|string $res): void;

    public function sendToAll(array|string $res): void;
}
