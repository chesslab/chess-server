<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class TakebackCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_takeback_foobar()
    {
        $this->expectException(ParserException::class);
        self::$parser->validate('/takeback foobar');
    }
}
