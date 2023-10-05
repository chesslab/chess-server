<?php

namespace ChessServer\Tests\Functional;

use ChessServer\Tests\AbstractFunctionalTestCase;
use React\Promise\Deferred;
use React\Socket\Connector;
use React\Socket\ConnectionInterface;

include_once __DIR__."/StartCommandTest.php";


class LegalCommandTest extends AbstractFunctionalTestCase
{
    public static $connector;

    public static $deferred;

    public static $promise;

    public static $startcommandtest;

    protected function setUp(): void
    {
        self::$connector = new Connector();
        self::$deferred = new Deferred();
        self::$promise = self::$deferred->promise();
        self::$startcommandtest = new StartCommandTest();
        self::$startcommandtest->setUp();
    }

    /**
     * @test
    */
    public function start_classical_fen(){
        self::$startcommandtest->start_classical_fen();
    } 

    /**
     * @depends start_classical_fen
     */

    public function legal_e2()
    {
        self::$connector->connect("$this->host:$this->port")->then(function (ConnectionInterface $conn) {
            $conn->on('data', function ($data) use ($conn) {
            self::$deferred->resolve($data);
            $conn->close();
            });
            $conn->write('/legal e2');
        });

        $response = \React\Async\await(self::$promise->then(function (string $result): string {
            return $result;
        }));

        $expected = '{"\/legal":{"color":"w","id":"P","fen":{"e3":"rnbqkbnr\/pppppppp\/8\/8\/8\/4P3\/PPPP1PPP\/RNBQKBNR b KQkq -","e4":"rnbqkbnr\/pppppppp\/8\/8\/4P3\/8\/PPPP1PPP\/RNBQKBNR b KQkq e3"}}}';

        $this->assertEquals($expected, $response);      
    }

}
