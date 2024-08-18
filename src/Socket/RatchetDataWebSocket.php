<?php

namespace ChessServer\Socket;

use ChessServer\Command\CommandParser;
use ChessServer\Command\Game\LeaveCommand;
use Ratchet\ConnectionInterface;

class RatchetDataWebSocket extends AbstractRatchetWebSocket
{
    public function __construct(CommandParser $parser)
    {
        parent::__construct($parser);
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clientStorage->detachById($conn->resourceId);

        $this->clientStorage->getLogger()->info('Closed connection', [
            'id' => $conn->resourceId,
            'n' => $this->clientStorage->count()
        ]);
    }
}
