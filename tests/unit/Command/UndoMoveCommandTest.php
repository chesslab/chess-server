<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Exception\ParserException;
use ChessServer\Tests\Unit\CommandTestCase;

class UndoMoveCommandTest extends CommandTestCase
{
    /**
     * @test
     */
    public function validate_undo_foo()
    {
        $this->expectException(ParserException::class);
        
        self::$parser->validate('/undo foo');
    }
}
