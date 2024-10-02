<?php

namespace ChessServer\Command;

use ChessServer\Db;
use ChessServer\Socket\AbstractSocket;
use Spatie\Async\Pool;

abstract class AbstractCommand
{
    const ANONYMOUS_USER = 'anonymous';

    protected string $name;

    protected string $description;

    protected array $params = [];

    protected Pool $pool;

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

    public function setPool(Pool $pool): AbstractCommand
    {
        $this->pool = $pool;

        return $this;
    }

    abstract public function validate(array $command);

    abstract public function run(AbstractSocket $socket, array $argv, int $id);
}
