<?php

namespace ChessServer\Command\Game\Mode;

use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Command\Game\Game;

class PlayMode extends AbstractMode
{
    const NAME = Game::MODE_PLAY;

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';

    const SUBMODE_FRIEND = 'friend';
    const SUBMODE_ONLINE = 'online';

    protected string $status;

    protected int $startedAt;

    protected int $updatedAt;

    protected array $timer;

    public function __construct(Game $game, array $resourceIds, string $jwt)
    {
        parent::__construct($game, $resourceIds, $jwt);

        $this->status = self::STATUS_PENDING;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStartedAt(): int
    {
        return $this->startedAt;
    }

    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    public function getTimer(): array
    {
        return $this->timer;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }

    public function setStartedAt(int $timestamp)
    {
        $this->startedAt = $timestamp;

        return $this;
    }

    public function setUpdatedAt(int $timestamp)
    {
        $this->updatedAt = $timestamp;

        return $this;
    }

    public function setTimer(array $timer)
    {
        $this->timer = $timer;

        return $this;
    }

    public function updateTimer(string $color)
    {
        $now = time();
        $diff = $now - $this->updatedAt;
        if ($this->game->getBoard()->turn === Color::B) {
            $this->timer[Color::W] -= $diff;
            $this->timer[Color::W] += $this->getJwtDecoded()->increment;
        } else {
            $this->timer[Color::B] -= $diff;
            $this->timer[Color::B] += $this->getJwtDecoded()->increment;
        }

        $this->updatedAt = $now;
    }
}
