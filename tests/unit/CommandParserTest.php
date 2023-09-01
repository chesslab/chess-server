<?php

namespace ChessServer\Tests\Unit;

use ChessServer\CommandContainer;
use ChessServer\CommandParser;
use ChessServer\Exception\ParserException;
use PHPUnit\Framework\TestCase;

class CommandParserTest extends TestCase
{
    protected static $parser;

    public function setUp(): void
    {
        self::$parser = new CommandParser(new CommandContainer());
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
