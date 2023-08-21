<?php

namespace ChessServer;

use Chess\Grandmaster;
use ChessServer\Command\LeaveCommand;
use ChessServer\Exception\InternalErrorException;
use ChessServer\Exception\ParserException;
use ChessServer\GameMode\PlayMode;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Socket implements MessageComponentInterface
{
    const DATA_FOLDER = __DIR__.'/../data';

    const STORAGE_FOLDER = __DIR__.'/../storage';

    private $log;

    private $cli;

    private $parser;

    private $gm;

    private $inboxStore;

    private $gameModeStorage;

    private $clients = [];

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();

        $this->log = new Logger($_ENV['BASE_URL']);
        $this->log->pushHandler(new StreamHandler(self::STORAGE_FOLDER.'/pchess.log', Logger::INFO));

        $this->cli = new CommandContainer;
        $this->parser = new CommandParser($this->cli);

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

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = $conn;

        $this->log->info('New connection', [
            'id' => $conn->resourceId,
            'n' => count($this->clients)
        ]);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $cmd = $this->parser->validate($msg);
        } catch (ParserException $e) {
            return $this->sendToOne($from->resourceId, [
                'error' => $e->getMessage(),
            ]);
        }

        try {
            $cmd->run($this, $this->parser->argv, $from);
        } catch (InternalErrorException $e) {
            return $this->sendToOne($from->resourceId, [
                'error' => 'Internal server error',
            ]);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        if ($gameMode = $this->gameModeStorage->getByResourceId($conn->resourceId)) {
            $this->gameModeStorage->delete($gameMode);
            $this->sendToMany(
                $gameMode->getResourceIds(),
                ['/leave' => LeaveCommand::ACTION_ACCEPT]
            );
        }

        if (isset($this->clients[$conn->resourceId])) {
            unset($this->clients[$conn->resourceId]);
        }

        $this->log->info('Closed connection', [
            'id' => $conn->resourceId,
            'n' => count($this->clients)
        ]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();

        $this->log->info('Occurred an error', ['message' => $e->getMessage()]);
    }

    public function sendToOne(int $resourceId, array $res)
    {
        if (isset($this->clients[$resourceId])) {
            $this->clients[$resourceId]->send(json_encode($res));

            $this->log->info('Sent message', [
                'id' => $resourceId,
                'cmd' => array_keys($res),
            ]);
        }
    }

    public function sendToMany(array $resourceIds, array $res)
    {
        foreach ($resourceIds as $resourceId) {
            $this->clients[$resourceId]->send(json_encode($res));
        }

        $this->log->info('Sent message', [
            'ids' => $resourceIds,
            'cmd' => array_keys($res),
        ]);
    }

    public function sendToAll()
    {
        $res = [
            'broadcast' => [
                'onlineGames' => $this->gameModeStorage
                    ->decodeByPlayMode(PlayMode::STATUS_PENDING, PlayMode::SUBMODE_ONLINE),
            ],
        ];

        foreach ($this->clients as $client) {
            $client->send(json_encode($res));
        }
    }
}
