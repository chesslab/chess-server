<?php

namespace ChessServer\Command;

use Spatie\Async\Task;

abstract class AbstractAsyncTask extends Task
{
    protected array $env;

    protected array $params;

    public function __construct(array $params = [])
    {
        $this->env = [
           'db' => [
               'driver' => $_ENV['DB_DRIVER'],
               'host' => $_ENV['DB_HOST'],
               'database' => $_ENV['DB_DATABASE'],
               'username' => $_ENV['DB_USERNAME'],
               'password' => $_ENV['DB_PASSWORD'],
           ],
       ];

       $this->params = $params;
    }
}
