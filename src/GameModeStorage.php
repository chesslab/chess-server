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
            if (in_array($resourceId, $this->current()->getResourceIds())) {
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

    public function set($gameMode): void
    {
        foreach ($resourceIds = $gameMode->getResourceIds() as $resourceId) {
            if ($found = $this->getByResourceId($resourceId)) {
                $this->detach($found);
            }
        }

        $this->attach($gameMode);
    }

    public function delete(AbstractMode $gameMode): void
    {
        $this->detach($gameMode);
    }
}
