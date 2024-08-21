<?php

namespace ChessServer\Socket;

use Monolog\Logger;

interface ClientStorageInterface
{
    public function getLogger(): Logger;

    public function detachById(int $id): void;

    public function sendToOne(int $id, array $res): void;

    public function sendToMany(array $ids, array $res): void;

    public function sendToAll(array $res): void;
}
