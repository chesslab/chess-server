<?php

namespace ChessServer\Socket;

use Chess\Grandmaster;
use ChessServer\Command\CommandParser;
use ChessServer\Game\GameModeStorage;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * ChesslaBlab
 *
 * Chess functionality to be extended by a socket.
 *
 * @author Jordi Bassagaña
 * @license GPL
 */
class ChesslaBlabSocket
{
    const DATA_FOLDER = __DIR__.'/../../data';

    const STORAGE_FOLDER = __DIR__.'/../../storage';

    /**
     * Logger.
     *
     * @var \Monolog\Logger
     */
    protected Logger $log;

    /**
     * Command parser.
     *
     * @var \ChessServer\Command\CommandParser
     */
    protected CommandParser $parser;

    /**
     * Chess grandmaster.
     *
     * @var \Chess\Grandmaster
     */
    protected Grandmaster $gm;

    /**
     * Game modes.
     *
     * @var \ChessServer\Game\GameModeStorage
     */
    protected GameModeStorage $gameModeStorage;

    /**
     * Clients.
     *
     * @var \ChessServer\Socket\ClientStorage
     */
    protected ClientStorage $clientStorage;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->log = new Logger('log');
        $this->log->pushHandler(new StreamHandler(self::STORAGE_FOLDER.'/pchess.log', Logger::INFO));

        $this->parser = new CommandParser();
        $this->gm = new Grandmaster(self::DATA_FOLDER.'/players.json');
        $this->gameModeStorage = new GameModeStorage();
        $this->clientStorage = new ClientStorage($this->log, $this->gameModeStorage);

        echo "Welcome to PHP Chess Server" . PHP_EOL;
        echo "Commands available:" . PHP_EOL;
        echo $this->parser->cli->help() . PHP_EOL;
        echo "Listening to commands..." . PHP_EOL;

        $this->log->info('Started the chess server');
    }

    /**
     * Returns the chess grandmaster.
     *
     * @return string
     */
    public function getGm(): Grandmaster
    {
        return $this->gm;
    }

    /**
     * Returns the game modes.
     *
     * @return string
     */
    public function getGameModeStorage(): GameModeStorage
    {
        return $this->gameModeStorage;
    }

    /**
     * Returns the clients.
     *
     * @return string
     */
    public function getClientsStorage(): ClientStorage
    {
        return $this->clientStorage;
    }
}
