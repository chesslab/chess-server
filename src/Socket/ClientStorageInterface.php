<?php

namespace ChessServer\Socket;

use Monolog\Logger;

interface ClientStorageInterface
{
    public function getLogger(): Logger;

    public function dettachById(int $id): void;

    public function sendToOne(int $id, array $res): void;

    public function sendToMany(array $ids, array $res): void;

    public function sendToAll(): void;
}
