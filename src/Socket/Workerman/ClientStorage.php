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

    public function send(array $ids, array $res): void
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

    public function broadcast(array $res): void
    {
        $json = json_encode($res);
        $this->rewind();
        while ($this->valid()) {
            $this->current()->send($json);
            $this->next();
        }
    }
}
