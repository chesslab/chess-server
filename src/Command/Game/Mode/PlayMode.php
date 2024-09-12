<?php

namespace ChessServer\Command\Game\Mode;

use Chess\Elo\Game as EloGame;
use Chess\Elo\Player as EloPlayer;
use Chess\Variant\Classical\PGN\AN\Color;
use Chess\Variant\Classical\PGN\AN\Termination;
use ChessServer\Command\Db;
use ChessServer\Command\Game\Game;
use ChessServer\Command\Game\PlayLanCommand;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class PlayMode extends AbstractMode
{
    const NAME = Game::MODE_PLAY;

    const STATUS_PENDING = 'pending';

    const STATUS_ACCEPTED = 'accepted';

    const SUBMODE_FRIEND = 'friend';

    const SUBMODE_ONLINE = 'online';

    protected string $jwt;

    protected Db $db;

    protected string $status;

    protected int $startedAt;

    protected int $updatedAt;

    protected array $timer;

    public function __construct(Game $game, array $resourceIds, string $jwt, Db $db)
    {
        parent::__construct($game, $resourceIds);

        $this->jwt = $jwt;
        $this->db = $db;
        $this->hash = hash('adler32', $jwt);
        $this->status = self::STATUS_PENDING;
    }

    public function getJwt()
    {
        return $this->jwt;
    }

    public function getJwtDecoded()
    {
        return JWT::decode($this->jwt, new Key($_ENV['JWT_SECRET'], 'HS256'));
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

    public function setJwt(array $payload)
    {
        $this->jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
        $this->hash = hash('adler32', $this->jwt);

        return $this;
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
        if ($this->game->getBoard()->turn === Color::B) {
            $this->timer[Color::W] -= $diff;
            $this->timer[Color::W] += $this->getJwtDecoded()->increment;
        } else {
            $this->timer[Color::B] -= $diff;
            $this->timer[Color::B] += $this->getJwtDecoded()->increment;
        }

        $this->updatedAt = $now;
    }

    protected function elo(string $result, int $i, int $j): array
    {
        $w = new EloPlayer($i);
        $b = new EloPlayer($j);
        $game =  (new EloGame($w, $b))->setK(32);
        if ($result === Termination::WHITE_WINS) {
            $game->setScore(1, 0);
        } elseif ($result === Termination::DRAW) {
            $game->setScore(0, 0);
        } elseif ($result === Termination::BLACK_WINS) {
            $game->setScore(0, 1);
        }
        $game->count();

        return [
            Color::W => $w->getRating(),
            Color::B => $b->getRating(),
        ];
    }

    protected function eloQuery(string $username, int $elo): void
    {
        $sql = "UPDATE users SET elo = :elo WHERE username = :username";
        $values= [
            [
                'param' => ":username",
                'value' => $username,
                'type' => \PDO::PARAM_STR,
            ],
            [
                'param' => ":elo",
                'value' => $elo,
                'type' => \PDO::PARAM_INT,
            ],
        ];

        $this->db->query($sql, $values);
    }

    public function res($params, $cmd)
    {
        switch (get_class($cmd)) {
            case PlayLanCommand::class:
                $isValid = $this->game->playLan($params['color'], $params['lan']);
                if ($isValid) {
                    if (isset($this->game->state()->end)) {
                        $decoded = $this->getJwtDecoded();
                        if ($decoded->elo->{Color::W} && $decoded->elo->{Color::B}) {
                            $elo = $this->elo(
                                $this->game->state()->end['result'],
                                $decoded->elo->{Color::W},
                                $decoded->elo->{Color::B}
                            );
                            $this->eloQuery($decoded->username->{Color::W}, $elo[Color::W]);
                            $this->eloQuery($decoded->username->{Color::B}, $elo[Color::B]);
                        }
                    } else {
                        $this->updateTimer($params['color']);
                    }
                }
                return [
                    $cmd->name => [
                      ...(array) $this->game->state(),
                      'variant' =>  $this->game->getVariant(),
                      'timer' => $this->timer,
                      'isValid' => $isValid,
                    ],
                ];

            default:
                return parent::res($params, $cmd);
        }
    }
}
