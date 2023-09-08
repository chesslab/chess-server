<?php

namespace ChessServer\Socket;

interface SendInterface
{
    /**
     * Send to one.
     *
     * @param int $resourceId
     * @param array $res
     */
    public function sendToOne(int $resourceId, array $res): void;

    /**
     * Send to many.
     *
     * @param int $resourceIds
     * @param array $res
     */
    public function sendToMany(array $resourceIds, array $res): void;

    /**
     * Send to all.
     *
     * @return void
     */
    public function sendToAll(): void;
}
