<?php

namespace ChessServer\Tests\Functional;

use ChessServer\Tests\AbstractFunctionalTestCase;
use React\Promise\Deferred;
use React\Socket\Connector;
use React\Socket\ConnectionInterface;


class LegalCommandTest extends AbstractFunctionalTestCase
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

    public function legal_b1()
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

        if($this->assertEquals($expected,$response)){
            self::$connector->connect("$this->host:$this->port")->then(function (ConnectionInterface $conn) {
                $conn->on('data', function ($data) use ($conn) {
                    self::$deferred->resolve($data);
                    $conn->close();
                });
                $conn->write('/legal b1');
            });

            $response = \React\Async\await(self::$promise->then(function (string $result): string {
                return $result;
            }));

            $expected = '{"\/legal":{"color":"w","id":"N","fen":{"a3":"rnbqkbnr\/pppppppp\/8\/8\/8\/N7\/PPPPPPPP\/R1BQKBNR b KQkq -","c3":"rnbqkbnr\/pppppppp\/8\/8\/8\/2N5\/PPPPPPPP\/R1BQKBNR b KQkq -"}}}';

            $this->assertEquals($expected, $response);
        }

    }

}
?>
