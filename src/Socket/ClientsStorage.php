<?php

namespace ChessServer\Socket;

use ChessServer\Game\GameModeStorage;
use ChessServer\Game\PlayMode;
use Monolog\Logger;

class ClientsStorage extends \SplObjectStorage
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
    private Logger $log;

    public function __construct(Logger $log, GameModeStorage $gameModeStorage)
    {
        $this->log = $log;
        $this->gameModeStorage = $gameModeStorage;
    }

    public function detachById(int $id): void
    {
        $this->rewind();
        while ($this->valid()) {
            if ($id === $this->current()->id) {
                $this->detach($this->current());
            }
            $this->next();
        }
    }

    public function sendToOne(int $id, array $res): void
    {
        $this->rewind();
        while ($this->valid()) {
            if ($id === $this->current()->id) {
                $this->current()->send(json_encode($res));
                $this->log->info('Sent message', [
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
            if (in_array($this->current()->id, $ids)) {
                $this->current()->send(json_encode($res));
                $this->log->info('Sent message', [
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
