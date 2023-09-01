<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class StartCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_start_foobar()
    {
        $this->expectException(ParserException::class);

        self::$parser->validate('/start foobar');
    }
}
