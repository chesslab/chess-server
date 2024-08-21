<?php

namespace ChessServer\Socket;

use Monolog\Logger;

interface BinaryClientStorageInterface
{
    public function getLogger(): Logger;

    public function detachById(int $id): void;

    public function transmitToOne(int $id, array $res): void;

    public function transmitToMany(array $ids, array $res): void;

    public function transmitToAll(array $res): void;
}
