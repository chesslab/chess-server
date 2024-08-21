<?php

namespace ChessServer\Socket\Workerman;

use ChessServer\Socket\ClientStorageInterface;
use Monolog\Logger;

class ClientStorage extends \SplObjectStorage implements ClientStorageInterface
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger(): Logger
    {
        return $this->logger;
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

    public function sendToOne(int $id, $res): void
    {
        $this->rewind();
        while ($this->valid()) {
            if ($id === $this->current()->id) {
                $res = is_array($res) ? json_encode($res) : $res;
                $this->current()->send($res);
                $this->logger->info('Sent message', [
                    'id' => $id,
                    'cmd' => is_array($res) ? array_keys($res) : [$res],
                ]);
            }
            $this->next();
        }
    }

    public function sendToMany(array $ids, $res): void
    {
        $res = is_array($res) ? json_encode($res) : $res;
        $this->rewind();
        while ($this->valid()) {
            if (in_array($this->current()->id, $ids)) {
                $this->current()->send($res);
                $this->logger->info('Sent message', [
                    'ids' => $ids,
                    'cmd' => is_array($res) ? array_keys($res) : [$res],
                ]);
            }
            $this->next();
        }
    }

    public function sendToAll($res): void
    {
        $res = is_array($res) ? json_encode($res) : $res;
        $this->rewind();
        while ($this->valid()) {
            $this->current()->send($res);
            $this->next();
        }
    }
}
