<?php

namespace ChessServer\Tests\Functional;

use PHPUnit\Framework\TestCase;
use React\Socket\Connector;
use React\Socket\ConnectionInterface;

class FooTest extends TestCase
{
    public static $host = '127.0.0.1';

    public static $port = '8080';

    /**
     * @test
     */
    public function foo()
    {
        $connector = new Connector();

        $connector->connect(self::$host.':'.self::$port)->then(function (ConnectionInterface $connection) {
            $connection->on('data', function ($data) {
                echo $data;
            });
            $connection->on('close', function () {
                echo '[CLOSED]' . PHP_EOL;
            });

            $connection->write("/start classical fen");
        }, function (Exception $e) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
        });

        $this->assertTrue(false);
    }
}
