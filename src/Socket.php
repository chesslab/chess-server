<?php

namespace ChessServer;

use Chess\Grandmaster;
use ChessServer\Exception\ParserException;
use ChessServer\GameMode\AbstractMode;
use ChessServer\GameMode\PlayMode;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
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

    private $gameModes = [];

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

    public function getGameModes()
    {
        return $this->gameModes;
    }

    public function getGameMode(int $id)
    {
        foreach ($this->gameModes as $key => $val) {
            if ($key === $id) {
                return $val;
            }
        }

        return null;
    }

    public function getGameModeByHash(string $hash)
    {
        foreach ($this->gameModes as $gameMode) {
            if ($hash === $gameMode->getHash()) {
                return $gameMode;
            }
        }

        return null;
    }

    public function getPendingGames()
    {
        $onlinePending = [];
        foreach ($this->gameModes as $gameMode) {
          if (is_a($gameMode, PlayMode::class)) {
            if ($gameMode->getState() === PlayMode::STATE_PENDING) {
                $decoded = JWT::decode($gameMode->getJwt(), $_ENV['JWT_SECRET'], array('HS256'));
                if ($decoded->submode === PlayMode::SUBMODE_ONLINE) {
                    $decoded->hash = $gameMode->getHash();
                    $onlinePending[] = $decoded;
                }
            }
          }
        }

        return $onlinePending;
    }

    public function setGameModes(array $ids, $gameMode)
    {
        foreach ($ids as $id) {
            $this->gameModes[$id] = $gameMode;
        }
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

        $cmd->run($this, $this->parser->argv, $from);
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->leaveGame($conn->resourceId);
        $this->deleteGameModes($conn->resourceId);
        $this->deleteClient($conn->resourceId);

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

    protected function leaveGame(int $resourceId)
    {
        if ($gameMode = $this->getGameMode($resourceId)) {
            $toId = null;
            $resourceIds = $gameMode->getResourceIds();
            if ($resourceIds[0] !== $resourceId) {
                $toId = $resourceIds[0];
            } elseif (isset($resourceIds[1]) && $resourceIds[1] !== $resourceId) {
                $toId = $resourceIds[1];
            }
            if ($toId) {
                $this->sendToOne($toId, ['/leave' => LeaveCommand::ACTION_ACCEPT]);
            }
        }
    }

    public function deleteGameModes(int $resourceId)
    {
        if ($gameMode = $this->getGameMode($resourceId)) {
            $resourceIds = $gameMode->getResourceIds();
            if (isset($resourceIds[0])) {
                if (isset($this->gameModes[$resourceIds[0]])) {
                    unset($this->gameModes[$resourceIds[0]]);
                }
            }
            if (isset($resourceIds[1])) {
                if (isset($this->gameModes[$resourceIds[1]])) {
                    unset($this->gameModes[$resourceIds[1]]);
                }
            }
        }
    }

    protected function deleteClient(int $resourceId)
    {
        if (isset($this->clients[$resourceId])) {
            unset($this->clients[$resourceId]);
        }
    }

    public function syncGameModeWith(AbstractMode $gameMode, ConnectionInterface $from)
    {
        if ($resourceIds = $gameMode->getResourceIds()) {
            if (count($resourceIds) === 1) {
                $resourceIds[] = $from->resourceId;
                $gameMode->setResourceIds($resourceIds);
                foreach ($resourceIds as $resourceId) {
                    $this->gameModes[$resourceId] = $gameMode;
                }
                return true;
            }
        }

        return false;
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
        $message = [
            'broadcast' => [
                'onlineGames' => $this->getPendingGames(),
            ],
        ];

        foreach ($this->clients as $client) {
            $client->send(json_encode($message));
        }
    }
}
