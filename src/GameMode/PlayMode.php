<?php

namespace ChessServer\GameMode;

use Chess\Game;
use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Command\DrawCommand;
use ChessServer\Command\LeaveCommand;
use ChessServer\Command\PlayLanCommand;
use ChessServer\Command\RematchCommand;
use ChessServer\Command\ResignCommand;
use ChessServer\Command\TakebackCommand;
use Firebase\JWT\JWT;

class PlayMode extends AbstractMode
{
    const NAME = Game::MODE_PLAY;

    const STATUS_PENDING = 'pending';

    const STATUS_ACCEPTED = 'accepted';

    const SUBMODE_FRIEND = 'friend';

    const SUBMODE_ONLINE = 'online';

    protected $jwt;

    protected string $status;

    protected int $startedAt;

    protected int $updatedAt;

    protected array $timer;

    public function __construct(Game $game, array $resourceIds, string $jwt)
    {
        parent::__construct($game, $resourceIds);

        $this->jwt = $jwt;
        $this->hash = md5($jwt);
        $this->status = self::STATUS_PENDING;
    }

    public function getJwt()
    {
        return $this->jwt;
    }

    public function getJwtDecoded()
    {
        return JWT::decode($this->jwt, $_ENV['JWT_SECRET'], array('HS256'));
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

    protected function updateTimer(string $color)
    {
        $now = time();
        $diff = $now - $this->updatedAt;
        if ($this->game->getBoard()->getTurn() === Color::B) {
            $this->timer[Color::W] -= $diff;
            $this->timer[Color::W] += $this->getJwtDecoded()->increment;
        } else {
            $this->timer[Color::B] -= $diff;
            $this->timer[Color::B] += $this->getJwtDecoded()->increment;
        }

        $this->updatedAt = $now;
    }

    public function res($argv, $cmd)
    {
        try {
            switch (get_class($cmd)) {
                case DrawCommand::class:
                    return [
                        $cmd->name => $argv[1],
                    ];
                case LeaveCommand::class:
                    return [
                        $cmd->name => $argv[1],
                    ];
                case RematchCommand::class:
                    return [
                        $cmd->name => $argv[1],
                    ];
                case ResignCommand::class:
                    return [
                        $cmd->name => $argv[1],
                    ];
                case TakebackCommand::class:
                    return [
                        $cmd->name => $argv[1],
                    ];
                case PlayLanCommand::class:
                    $this->game->playLan($argv[1], $argv[2]);
                    $this->updateTimer($argv[1]);
                    return [
                        $cmd->name => [
                          ... (array) $this->game->state(),
                          'variant' =>  $this->game->getVariant(),
                          // play mode information
                          'timer' => $this->timer,
                        ],
                    ];
                default:
                    return parent::res($argv, $cmd);
            }
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
