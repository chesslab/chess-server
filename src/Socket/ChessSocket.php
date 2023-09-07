<?php

namespace ChessServer\Socket;

use Chess\Grandmaster;
use ChessServer\Command\CommandParser;
use ChessServer\Game\GameModeStorage;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ChessSocket
{
    const DATA_FOLDER = __DIR__.'/../../data';

    const STORAGE_FOLDER = __DIR__.'/../../storage';

    protected $log;

    protected $parser;

    protected $gm;

    protected $inboxStore;

    protected $gameModeStorage;

    protected $clients = [];

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../../');
        $dotenv->load();

        $this->log = new Logger($_ENV['BASE_URL']);
        $this->log->pushHandler(new StreamHandler(self::STORAGE_FOLDER.'/pchess.log', Logger::INFO));

        $this->parser = new CommandParser();

        $this->gm = new Grandmaster(self::DATA_FOLDER.'/players.json');

        $databaseDirectory = self::STORAGE_FOLDER;
        $this->inboxStore = new \SleekDB\Store("inbox", self::STORAGE_FOLDER);

        $this->gameModeStorage = new GameModeStorage();

        echo "Welcome to PHP Chess Server" . PHP_EOL;
        echo "Commands available:" . PHP_EOL;
        echo $this->parser->cli->help() . PHP_EOL;
        echo "Listening to commands..." . PHP_EOL;

        $this->log->info('Started the chess server');
    }

    public function getGm()
    {
        return $this->gm;
    }

    public function getInboxStore()
    {
        return $this->inboxStore;
    }

    public function getGameModeStorage()
    {
        return $this->gameModeStorage;
    }
}
