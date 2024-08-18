<?php

namespace ChessServer\Socket;

use ChessServer\Command\CommandParser;
use ChessServer\Command\Data\CommandContainer;
use ChessServer\Command\Data\Db;
use Workerman\Timer;

class WorkermanDataWebSocket extends AbstractWorkermanWebSocket
{
    public function __construct(string $socketName, array $context, CommandParser $parser)
    {
        parent::__construct($socketName, $context, $parser);

        $this->start()->connect()->message()->error()->close();
    }

    protected function start()
    {
        $this->worker->onWorkerStart = function() {
            Timer::add(5, function() {
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
                        $this->setParser(new CommandParser(new CommandContainer($db)));
                        $this->getClientStorage()->getLogger()->info('Successfully reconnected to Chess Data');
                    } catch(\PDOException $e) {
                        // Trying to connect to Chess Data...
                    }
                }
            });
        };

        return $this;
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
