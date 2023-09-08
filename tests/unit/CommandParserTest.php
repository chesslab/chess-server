<?php

namespace ChessServer\Tests\Unit;

use ChessServer\Command\CommandParser;
use ChessServer\Command\StartCommand;
use ChessServer\Exception\ParserException;
use PHPUnit\Framework\TestCase;

class CommandParserTest extends TestCase
{
    protected static $parser;

    public function setUp(): void
    {
        self::$parser = new CommandParser();
    }

    /**
     * @test
     */
    public function validate_start_foobar()
    {
        $this->expectException(ParserException::class);

        self::$parser->validate('/start foobar');
    }

    /**
     * @test
     */
    public function validate_start_classical_foobar()
    {
        $this->expectException(ParserException::class);

        self::$parser->validate('/start classical foobar');
    }

    /**
     * @test
     */
    public function validate_start_classical_fen()
    {
        $this->assertInstanceOf(StartCommand::class, self::$parser->validate('/start classical fen'));
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
}
