<?php

namespace ChessServer\Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class AbstractFunctionalTestCase extends TestCase
{
    protected string $host;

    protected string $port;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();

        $this->host = '127.0.0.1';
        $this->port = $_ENV['TCP_PORT'];
    }
}
