<?php

namespace ChessServer\Socket\Ratchet;

use ChessServer\Command\CommandParser;
use ChessServer\Command\Data\CommandContainer;
use ChessServer\Command\Data\Db;
use Ratchet\ConnectionInterface;

class RatchetDataWebSocket extends AbstractRatchetWebSocket
{
    private $timeInterval = 5;

    public function __construct(CommandParser $parser)
    {
        parent::__construct($parser);

        $this->loop->addPeriodicTimer($this->timeInterval, function() {
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
