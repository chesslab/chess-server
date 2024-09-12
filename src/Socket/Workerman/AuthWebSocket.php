<?php

namespace ChessServer\Socket\Workerman;

use ChessServer\Command\Parser;
use ChessServer\Socket\DbReconnectTrait;
use Workerman\Timer;

class AuthWebSocket extends AbstractWebSocket
{
    use DbReconnectTrait;

    public function __construct(string $socketName, array $context, Parser $parser)
    {
        parent::__construct($socketName, $context, $parser);

        $this->worker->onWorkerStart = function() {
            Timer::add($this->timeInterval, function() {
                $this->reconnect();
            });
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
