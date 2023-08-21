<?php

namespace ChessServer\Command;

use ChessServer\Socket;
use Ratchet\ConnectionInterface;

abstract class AbstractCommand
{
    protected $name;

    protected $description;

    protected $params;

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    abstract public function validate(array $command);

    abstract public function run(Socket $socket, array $argv, ConnectionInterface $from);
}
