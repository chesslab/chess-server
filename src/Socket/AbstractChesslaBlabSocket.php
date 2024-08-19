<?php

namespace ChessServer\Socket;

use Chess\Computer\GrandmasterMove;
use ChessServer\Command\Parser;
use ChessServer\Command\Game\GameModeStorage;

abstract class AbstractChesslaBlabSocket
{
    const DATA_FOLDER = __DIR__.'/../../data';

    protected Parser $parser;

    protected GrandmasterMove $gmMove;

    protected GameModeStorage $gameModeStorage;

    protected ClientStorageInterface $clientStorage;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
        $this->gmMove = new GrandmasterMove(self::DATA_FOLDER.'/players.json');
        $this->gameModeStorage = new GameModeStorage();

        echo "Welcome to PHP Chess Server" . PHP_EOL;
        echo "Commands available:" . PHP_EOL;
        echo $this->parser->cli->help() . PHP_EOL;
        echo "Listening to commands..." . PHP_EOL;
    }

    public function init(ClientStorageInterface $clientStorage): AbstractChesslaBlabSocket
    {
        $this->clientStorage = $clientStorage;

        return $this;
    }

    public function getGmMove(): GrandmasterMove
    {
        return $this->gmMove;
    }

    public function getGameModeStorage(): GameModeStorage
    {
        return $this->gameModeStorage;
    }

    public function getClientStorage(): ClientStorageInterface
    {
        return $this->clientStorage;
    }

    public function setParser(Parser $parser)
    {
        $this->parser = $parser;
    }
}
