<?php

namespace ChessServer\Socket\Workerman;

use ChessServer\Command\Parser;
use ChessServer\Command\Data\Cli;
use ChessServer\Command\Data\Db;
use Workerman\Timer;

class DataWebSocket extends AbstractWebSocket
{
    private $timeInterval = 5;

    public function __construct(string $socketName, array $context, Parser $parser)
    {
        parent::__construct($socketName, $context, $parser);

        $this->worker->onWorkerStart = function() {
            Timer::add($this->timeInterval, function() {
                try {
                    $this->parser->cli->getDb()->getPdo()->getAttribute(\PDO::ATTR_SERVER_INFO);
                } catch(\PDOException $e) {
                    try {
                        $db = new Db([
                           'driver' => $_ENV['DB_DRIVER'],
                           'host' => $_ENV['DB_HOST'],
                           'database' => $_ENV['DB_DATABASE'],
                           'username' => $_ENV['DB_USERNAME'],
                           'password' => $_ENV['DB_PASSWORD'],
                        ]);
                        $this->setParser(new Parser(new Cli($db)));
                        $this->getClientStorage()->getLogger()->info('Successfully reconnected to Chess Data');
                    } catch(\PDOException $e) {
                        // Trying to connect to Chess Data...
                    }
                }
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
                'n' => $this->clientStorage->count()
            ]);
        };

        return $this;
    }
}
