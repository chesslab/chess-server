<?php

namespace ChessServer\Command;

use ChessServer\Socket\AbstractSocket;

abstract class AbstractCommand
{
    const ANONYMOUS_USER = 'anonymous';

    protected string $name;

    protected string $description;

    protected array $params = [];

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    abstract public function validate(array $command);

    abstract public function run(AbstractSocket $socket, array $argv, int $id);
}
