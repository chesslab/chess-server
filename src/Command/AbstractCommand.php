<?php

namespace ChessServer\Command;

use ChessServer\Command\Db;
use ChessServer\Socket\AbstractSocket;

abstract class AbstractCommand
{
    const ANONYMOUS_USER = 'anonymous';

    protected string $name;

    protected string $description;

    protected array $params = [];

    protected Db $db;

    public function __construct(Db $db = null)
    {
        $this->db = $db;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    abstract public function validate(array $command);

    abstract public function run(AbstractSocket $socket, array $argv, int $id);
}
