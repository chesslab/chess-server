<?php

namespace ChessServer\Socket\Ratchet;

use ChessServer\Command\Parser;
use Ratchet\ConnectionInterface;

class BinaryWebSocket extends AbstractWebSocket
{
    public function __construct(Parser $parser)
    {
        parent::__construct($parser);
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clientStorage->detachById($conn->resourceId);
        $this->clientStorage->getLogger()->info('Closed connection', [
            'id' => $conn->resourceId,
            'n' => $this->clientStorage->count(),
        ]);
    }
}
