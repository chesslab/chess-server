<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\Parser;
use ChessServer\Command\Game\Cli;
use ChessServer\Command\Game\Async\RestartCommand;
use ChessServer\Command\Game\Sync\AcceptPlayRequestCommand;
use ChessServer\Command\Game\Sync\StartCommand;
use ChessServer\Exception\ParserException;
use PHPUnit\Framework\TestCase;
use Spatie\Async\Pool;

class ParserTest extends TestCase
{
    protected static $parser;

    public function setUp(): void
    {
        $pool = $this->getMockBuilder(Pool::class)
            ->disableOriginalConstructor()
            ->getMock();

        self::$parser = new Parser(new Cli($pool));
    }

    /**
     * @test
     */
    public function validate_start_foo_bar()
    {
        $this->expectException(ParserException::class);

        self::$parser->validate('/start foo bar');
    }

    /**
     * @test
     */

    public function validate_restart()
    {
        $this->expectException(ParserException::class);

        self::$parser->validate('/restart');
    }

    /**
     * @test
     */

    public function validate_takeback_foobar()
    {
        $this->expectException(ParserException::class);

        self::$parser->validate('/takeback foobar');
    }


    /**
     * @test
     */
    public function validate_undo_foo()
    {
        $this->expectException(ParserException::class);

        self::$parser->validate('/undo foo');
    }

    /**
     * @test
     */
    public function validate_restart_foobar()
    {
        $this->assertInstanceOf(RestartCommand::class, self::$parser->validate('/restart foobar'));
    }

    /**
     * @test
     */
    public function validate_restart_foo_bar()
    {
        $this->expectException(ParserException::class);

        self::$parser->validate('/restart foo bar');
    }

    /**
     * @test
     */
    public function validate_accept_foobar()
    {
        $this->assertInstanceOf(AcceptPlayRequestCommand::class, self::$parser->validate('/accept foobar'));
    }

    /**
     * @test
     */
    public function validate_accept()
    {
        $this->expectException(ParserException::class);

        self::$parser->validate('/accept');
    }

    /**
     * @test
     */
    public function validate_accept_foo_bar()
    {
        $this->expectException(ParserException::class);

        self::$parser->validate('/accept foo bar');
    }
}
