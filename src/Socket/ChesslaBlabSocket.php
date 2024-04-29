<?php

namespace ChessServer\Socket;

use Chess\Computer\GrandmasterComputer;
use ChessServer\Command\CommandParser;
use ChessServer\Game\GameModeStorage;

/**
 * ChesslaBlabSocket
 *
 * @author Jordi Bassagaña
 * @license GPL
 */
class ChesslaBlabSocket
{
    const DATA_FOLDER = __DIR__.'/../../data';

    /**
     * Command parser.
     *
     * @var \ChessServer\Command\CommandParser
     */
    protected CommandParser $parser;

    /**
     * Grandmaster computer.
     *
     * @var \Chess\Computer\GrandmasterComputer
     */
    protected GrandmasterComputer $gmComputer;

    /**
     * Game modes.
     *
     * @var \ChessServer\Game\GameModeStorage
     */
    protected GameModeStorage $gameModeStorage;

    /**
     * Clients.
     *
     * @var \ChessServer\Socket\ClientStorageInterface
     */
    protected ClientStorageInterface $clientStorage;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->parser = new CommandParser();
        $this->gmComputer = new GrandmasterComputer(self::DATA_FOLDER.'/players.json');
        $this->gameModeStorage = new GameModeStorage();

        echo "Welcome to PHP Chess Server" . PHP_EOL;
        echo "Commands available:" . PHP_EOL;
        echo $this->parser->cli->help() . PHP_EOL;
        echo "Listening to commands..." . PHP_EOL;
    }

    public function init(ClientStorageInterface $clientStorage): ChesslaBlabSocket
    {
        $this->clientStorage = $clientStorage;

        return $this;
    }

    /**
     * Returns the grandmaster computer.
     *
     * @return string
     */
    public function getGmComputer(): GrandmasterComputer
    {
        return $this->gmComputer;
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
    public function getClientStorage(): ClientStorageInterface
    {
        return $this->clientStorage;
    }
}
