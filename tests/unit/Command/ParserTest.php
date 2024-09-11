<?php

namespace ChessServer\Tests\Unit\Command;

use ChessServer\Command\Db;
use ChessServer\Command\Parser;
use ChessServer\Command\Game\AcceptPlayRequestCommand;
use ChessServer\Command\Game\Cli;
use ChessServer\Command\Game\RestartCommand;
use ChessServer\Command\Game\StartCommand;
use ChessServer\Exception\ParserException;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    protected static $db;

    protected static $parser;

    public static function setUpBeforeClass(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../../../');
        $dotenv->load();

        $db = new Db([
           'driver' => $_ENV['DB_DRIVER'],
           'host' => $_ENV['DB_HOST'],
           'database' => $_ENV['DB_DATABASE'],
           'username' => $_ENV['DB_USERNAME'],
           'password' => $_ENV['DB_PASSWORD'],
        ]);

        self::$parser = new Parser(new Cli($db));
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
