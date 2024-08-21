<?php

namespace ChessServer\Socket\Workerman;

use ChessServer\Socket\BinaryClientStorageInterface;
use Monolog\Logger;

class BinaryClientStorage extends \SplObjectStorage implements BinaryClientStorageInterface
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

    public function transmitToOne(int $id, array $res): void
    {
        $this->rewind();
        while ($this->valid()) {
            if ($id === $this->current()->id) {
                $this->current()->send(json_encode($res));
                $this->logger->info('Sent message', [
                    'id' => $id,
                    'cmd' => array_keys($res),
                ]);
            }
            $this->next();
        }
    }

    public function transmitToMany(array $ids, array $res): void
    {
        $json = json_encode($res);
        $this->rewind();
        while ($this->valid()) {
            if (in_array($this->current()->id, $ids)) {
                $this->current()->send($json);
                $this->logger->info('Sent message', [
                    'ids' => $ids,
                    'cmd' => array_keys($res),
                ]);
            }
            $this->next();
        }
    }

    public function transmitToAll(array $res): void
    {
        $json = json_encode($res);
        $this->rewind();
        while ($this->valid()) {
            $this->current()->send($json);
            $this->next();
        }
    }
}
