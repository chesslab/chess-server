<?php

namespace ChessServer\Socket\Ratchet;

use ChessServer\Command\Parser;
use ChessServer\Socket\DbReconnectTrait;
use Ratchet\ConnectionInterface;

class AuthWebSocket extends AbstractWebSocket
{
    use DbReconnectTrait;

    private $timeInterval = 5;

    public function __construct(Parser $parser)
    {
        parent::__construct($parser);

        $this->loop->addPeriodicTimer($this->timeInterval, function() {
            $this->reconnect();
        });
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
