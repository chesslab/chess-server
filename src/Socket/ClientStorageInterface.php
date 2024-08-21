<?php

namespace ChessServer\Socket;

use Monolog\Logger;

interface ClientStorageInterface
{
    public function getLogger(): Logger;

    public function detachById(int $id): void;

    public function sendToOne(int $id, $res): void;

    public function sendToMany(array $ids, $res): void;

    public function sendToAll($res): void;
}
