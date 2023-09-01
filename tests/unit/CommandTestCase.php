<?php

namespace ChessServer\Tests\Unit;

use ChessServer\CommandContainer;
use ChessServer\CommandParser;
use PHPUnit\Framework\TestCase;

class CommandTestCase extends TestCase
{
    protected static $parser;

    public function setUp(): void
    {
        self::$parser = new CommandParser(new CommandContainer());
    }
}
