<?php

namespace ChessServer\Game;

class GameModeStorage extends \SplObjectStorage
{
    public function getById(int $resourceId): ?AbstractMode
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

    public function decodeByPlayMode(string $status, string $submode): array
    {
        $items = [];
        $this->rewind();
        while ($this->valid()) {
            if (is_a($this->current(), PlayMode::class)) {
                if ($this->current()->getStatus() === $status) {
                    $decoded = $this->current()->getJwtDecoded();
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
            if ($found = $this->getById($resourceId)) {
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
