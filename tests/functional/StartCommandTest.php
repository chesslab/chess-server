<?php

namespace ChessServer\Tests\Functional;

use ChessServer\Tests\AbstractFunctionalTestCase;
use React\Promise\Deferred;
use React\Socket\Connector;
use React\Socket\ConnectionInterface;

class StartCommandTest extends AbstractFunctionalTestCase
{
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
        self::$connector->connect("$this->host:$this->port")->then(function (ConnectionInterface $conn) {
            $conn->on('data', function ($data) use ($conn) {
                self::$deferred->resolve($data);
                $conn->close();
            });
            $conn->write("/start classical fen");
        });

        $response = \React\Async\await(self::$promise->then(function (string $result): string {
            return $result;
        }));

        $expected = '{"\/start":{"variant":"classical","mode":"fen","fen":"rnbqkbnr\/pppppppp\/8\/8\/8\/8\/PPPPPPPP\/RNBQKBNR w KQkq -"}}';

        $this->assertEquals($expected, $response);
    }
}
