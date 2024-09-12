<?php

namespace ChessServer\Socket\Workerman;

use ChessServer\Command\Parser;
use ChessServer\Command\Data\Cli;
use ChessServer\Command\Data\Db;
use ChessServer\Socket\DbReconnectTrait;
use Workerman\Timer;

class DataWebSocket extends AbstractWebSocket
{
    use DbReconnectTrait;

    private $timeInterval = 5;

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
