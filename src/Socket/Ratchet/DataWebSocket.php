<?php

namespace ChessServer\Socket\Ratchet;

use ChessServer\Command\Parser;
use Ratchet\ConnectionInterface;

class DataWebSocket extends AbstractWebSocket
{
    public function onClose(ConnectionInterface $conn)
    {
        $this->clientStorage->detachById($conn->resourceId);
        $this->clientStorage->getLogger()->info('Closed connection', [
            'id' => $conn->resourceId,
            'n' => $this->clientStorage->count(),
        ]);
    }
}
