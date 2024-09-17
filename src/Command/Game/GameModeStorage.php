<?php

namespace ChessServer\Command\Game;

use ChessServer\Command\Game\Mode\AbstractMode;
use ChessServer\Command\Game\Mode\PlayMode;

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

    public function getByUid(string $uid): ?AbstractMode
    {
        $this->rewind();
        while ($this->valid()) {
            if ($uid === $this->current()->getUid()) {
                return $this->current();
            }
            $this->next();
        }

        return null;
    }

    public function getByJwt(string $jwt): ?AbstractMode
    {
        $this->rewind();
        while ($this->valid()) {
            if ($jwt === $this->current()->getJwt()) {
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
                        $decoded->uid = $this->current()->getUid();
                        $decoded->jwt = $this->current()->getJwt();
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
