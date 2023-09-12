<?php

namespace ChessServer\Tests\Functional;

use PHPUnit\Framework\TestCase;
use React\Socket\Connector;
use React\Socket\ConnectionInterface;

class StartCommandTest extends TestCase
{
    public static $host = '127.0.0.1';

    public static $port = '8080';

    /**
     * @test
     */
    public function start_classical_fen()
    {
        $expected = '{"\/start":{"variant":"classical","mode":"fen","fen":"rnbqkbnr\/pppppppp\/8\/8\/8\/8\/PPPPPPPP\/RNBQKBNR w KQkq -"}}';

        $connector = new Connector();

        $deferred = new \React\Promise\Deferred();

        $promise = $deferred->promise();

        $connector->connect(self::$host.':'.self::$port)->then(function (ConnectionInterface $connection) use ($deferred) {
            $promise = $deferred->promise();
            $connection->on('data', function ($data) use ($connection, $deferred) {
                $deferred->resolve($data);
                $connection->close();
            });
            $connection->write("/start classical fen");
        }, function (Exception $e) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
        });

        $response = \React\Async\await($promise->then(function (string $result): string {
            return $result;
        }, function (\Throwable $e): void {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
        }));

        $this->assertEquals($expected, $response);
    }
}
