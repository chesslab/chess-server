<?php

namespace ChessServer\Socket\Workerman;

use ChessServer\Command\Parser;
use Workerman\Protocols\Websocket;

class BinaryWebSocket extends AbstractWebSocket
{
    public function __construct(string $socketName, array $context, Parser $parser)
    {
        parent::__construct($socketName, $context, $parser);

        $this->worker->onConnect = function($conn) {
            $conn->websocketType = Websocket::BINARY_TYPE_ARRAYBUFFER;
        };

        $this->connect()->message()->error()->close();
    }

    protected function close()
    {
        $this->worker->onClose = function ($conn) {
            $this->clientStorage->detachById($conn->id);
            $this->clientStorage->getLogger()->info('Closed connection', [
                'id' => $conn->id,
                'n' => $this->clientStorage->count(),
            ]);
        };

        return $this;
    }
}
