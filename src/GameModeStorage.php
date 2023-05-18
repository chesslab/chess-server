<?php

namespace ChessServer;

use ChessServer\GameMode\AbstractMode;
use ChessServer\GameMode\PlayMode;
use Firebase\JWT\JWT;

class GameModeStorage extends \SplObjectStorage
{
    public function getByResourceId(int $resourceId): ?AbstractMode
    {
        $this->rewind();
        while ($this->valid()) {
            if ($resourceId === $this->getInfo()) {
                return $this->current();
            }
            $this->next();
        }

        return null;
    }

    public function getByHash(string $hash): ?AbstractMode
    {
        $this->rewind();
        while ($this->valid()) {
            if ($hash === $this->current()->getHash()) {
                return $this->current();
            }
            $this->next();
        }

        return null;
    }

    public function decodeByPlayMode(string $state, string $submode): array
    {
        $items = [];
        $this->rewind();
        while ($this->valid()) {
            if (is_a($this->current(), PlayMode::class)) {
                if ($this->current()->getState() === $state) {
                    $decoded = JWT::decode(
                        $this->current()->getJwt(),
                        $_ENV['JWT_SECRET'], array('HS256')
                    );
                    if ($decoded->submode === $submode) {
                        $decoded->hash = $this->current()->getHash();
                        $items[] = $decoded;
                    }
                }
            }
            $this->next();
        }

        return $items;
    }

    public function set(array $resourceIds, $gameMode): void
    {
        foreach ($resourceIds as $resourceId) {
            if ($prev = $this->getByResourceId($resourceId)) {
                $this->detach($prev);
            }
            $this->attach($gameMode, $resourceId);
        }
    }

    public function delete(int $resourceId): void
    {
        $this->rewind();
        while ($this->valid()) {
            if ($resourceId === $this->getInfo()) {
                $this->detach($this->current());
            }
            $this->next();
        }
    }
}
