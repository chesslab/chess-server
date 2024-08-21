<?php

namespace ChessServer\Socket;

interface BinaryClientStorageInterface extends ClientStorageInterface
{
    public function transmitToOne(int $id, array $res): void;

    public function transmitToMany(array $ids, array $res): void;

    public function transmitToAll(array $res): void;
}
