<?php

namespace ChessServer\Socket;

trait DbReconnectTrait
{
    public function reconnect(): void
    {
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
            }
        }
    }
}
