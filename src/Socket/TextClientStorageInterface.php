<?php

namespace ChessServer\Socket;

interface TextClientStorageInterface extends ClientStorageInterface
{
    public function sendToOne(int $id, array $res): void;

    public function sendToMany(array $ids, array $res): void;

    public function sendToAll(array $res): void;
}
