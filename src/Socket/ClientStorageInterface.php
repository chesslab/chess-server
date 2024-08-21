<?php

namespace ChessServer\Socket;

use Monolog\Logger;

interface ClientStorageInterface
{
    public function getLogger(): Logger;

    public function detachById(int $id): void;

    public function sendToOne(int $id, $data): void;

    public function sendToMany(array $ids, $data): void;

    public function sendToAll($data): void;
}
