<?php

namespace ChessServer;

use Chess\Grandmaster;
use ChessServer\Command\AcceptPlayRequestCommand;
use ChessServer\Command\InboxCommand;
use ChessServer\Command\DrawCommand;
use ChessServer\Command\LeaveCommand;
use ChessServer\Command\OnlineGamesCommand;
use ChessServer\Command\PlayLanCommand;
use ChessServer\Command\RandomizerCommand;
use ChessServer\Command\RematchCommand;
use ChessServer\Command\ResignCommand;
use ChessServer\Command\RestartCommand;
use ChessServer\Command\StartCommand;
use ChessServer\Command\TakebackCommand;
use ChessServer\Command\UndoCommand;
use ChessServer\Exception\ParserException;
use ChessServer\GameMode\AbstractMode;
use ChessServer\GameMode\AnalysisMode;
use ChessServer\GameMode\GmMode;
use ChessServer\GameMode\FenMode;
use ChessServer\GameMode\PgnMode;
use ChessServer\GameMode\PlayMode;
use ChessServer\GameMode\StockfishMode;
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

    private $clients = [];

    private $gameModes = [];

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

        $gameMode = $this->gameModeByResourceId($from->resourceId);

        if (is_a($cmd, AcceptPlayRequestCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, DrawCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, InboxCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, LeaveCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, OnlineGamesCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, PlayLanCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, RandomizerCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, RematchCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, ResignCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, RestartCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, StartCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, TakebackCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, UndoCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            } elseif (
                is_a($gameMode, AnalysisMode::class) ||
                is_a($gameMode, FenMode::class) ||
                is_a($gameMode, GmMode::class) ||
                is_a($gameMode, PgnMode::class) ||
                is_a($gameMode, StockfishMode::class)
            ) {
                return $this->sendToOne(
                    $from->resourceId,
                    $gameMode->res($this->parser->argv, $cmd)
                );
            }
        } elseif ($gameMode) {
            return $this->sendToOne(
                $from->resourceId,
                $this->gameModes[$from->resourceId]->res($this->parser->argv, $cmd)
            );
        }
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

    public function getGm()
    {
        return $this->gm;
    }

    public function getInboxStore()
    {
        return $this->inboxStore;
    }

    public function gameModeByHash(string $hash)
    {
        foreach ($this->gameModes as $gameMode) {
            if ($hash === $gameMode->getHash()) {
                return $gameMode;
            }
        }

        return null;
    }

    public function setGameModes(array $ids, $gameMode)
    {
        foreach ($ids as $id) {
            $this->gameModes[$id] = $gameMode;
        }
    }

    public function playModesArrayByState(string $state)
    {
        $result = [];
        foreach ($this->gameModes as $gameMode) {
          if (is_a($gameMode, PlayMode::class)) {
            if ($gameMode->getState() === $state) {
                $decoded = JWT::decode($gameMode->getJwt(), $_ENV['JWT_SECRET'], array('HS256'));
                if ($decoded->submode === PlayMode::SUBMODE_ONLINE) {
                    $decoded->hash = $gameMode->getHash();
                    $result[] = $decoded;
                }
            }
          }
        }

        return $result;
    }

    public function gameModeByResourceId(int $id)
    {
        foreach ($this->gameModes as $key => $val) {
            if ($key === $id) {
                return $val;
            }
        }

        return null;
    }

    protected function leaveGame(int $resourceId)
    {
        if ($gameMode = $this->gameModeByResourceId($resourceId)) {
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
        if ($gameMode = $this->gameModeByResourceId($resourceId)) {
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

    public function broadcast()
    {
        $message = [
            'broadcast' => [
                'onlineGames' => $this->playModesArrayByState(PlayMode::STATE_PENDING),
            ],
        ];

        foreach ($this->clients as $client) {
            $client->send(json_encode($message));
        }
    }
}
