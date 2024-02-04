<?php

namespace ChessServer\Socket;

use ChessServer\Game\GameModeStorage;
use ChessServer\Game\PlayMode;
use Monolog\Logger;

class RatchetClientStorage extends \SplObjectStorage implements ClientStorageInterface
{
    /**
     * Game modes.
     *
     * @var \ChessServer\Game\GameModeStorage
     */
    protected GameModeStorage $gameModeStorage;

    /**
     * Logger.
     *
     * @var \Monolog\Logger
     */
    private Logger $logger;

    public function __construct(GameModeStorage $gameModeStorage, Logger $logger)
    {
        $this->gameModeStorage = $gameModeStorage;
        $this->logger = $logger;
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    public function dettachById(int $id): void
    {
        $this->rewind();
        while ($this->valid()) {
            if ($id === $this->current()->resourceId) {
                $this->detach($this->current());
            }
            $this->next();
        }
    }

    public function sendToOne(int $id, array $res): void
    {
        $this->rewind();
        while ($this->valid()) {
            if ($id === $this->current()->resourceId) {
                $this->current()->send(json_encode($res));
                $this->logger->info('Sent message', [
                    'id' => $id,
                    'cmd' => array_keys($res),
                ]);
            }
            $this->next();
        }
    }

    public function sendToMany(array $ids, array $res): void
    {
        $this->rewind();
        while ($this->valid()) {
            if (in_array($this->current()->resourceId, $ids)) {
                $this->current()->send(json_encode($res));
                $this->logger->info('Sent message', [
                    'ids' => $ids,
                    'cmd' => array_keys($res),
                ]);
            }
            $this->next();
        }
    }

    public function sendToAll(): void
    {
        $res = [
            'broadcast' => [
                'onlineGames' => $this->gameModeStorage
                    ->decodeByPlayMode(PlayMode::STATUS_PENDING, PlayMode::SUBMODE_ONLINE),
            ],
        ];

        $this->rewind();
        while ($this->valid()) {
            $this->current()->send(json_encode($res));
            $this->next();
        }
    }
}
