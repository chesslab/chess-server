<?php

namespace ChessServer\Tests\Functional;

use PHPUnit\Framework\TestCase;
use React\Promise\Deferred;
use React\Socket\Connector;
use React\Socket\ConnectionInterface;

class StartCommandTest extends TestCase
{
    public static $host = '127.0.0.1';

    public static $port = '8080';

    public static $connector;

    public static $deferred;

    public static $promise;

    protected function setUp(): void
    {
        self::$connector = new Connector();
        self::$deferred = new Deferred();
        self::$promise = self::$deferred->promise();
    }

    /**
     * @test
     */
    public function start_classical_fen()
    {
        $expected = '{"\/start":{"variant":"classical","mode":"fen","fen":"rnbqkbnr\/pppppppp\/8\/8\/8\/8\/PPPPPPPP\/RNBQKBNR w KQkq -"}}';

        self::$connector->connect(self::$host.':'.self::$port)->then(function (ConnectionInterface $connection) {
            $connection->on('data', function ($data) use ($connection) {
                self::$deferred->resolve($data);
                $connection->close();
            });
            $connection->write("/start classical fen");
        }, function (Exception $e) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
        });

        $response = \React\Async\await(self::$promise->then(function (string $result): string {
            return $result;
        }, function (\Throwable $e): void {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
        }));

        $this->assertEquals($expected, $response);
    }
}
