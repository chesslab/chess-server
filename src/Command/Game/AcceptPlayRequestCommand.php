<?php

namespace ChessServer\Command\Game;

use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Command\AbstractCommand;
use ChessServer\Command\Game\Mode\PlayMode;
use ChessServer\Socket\AbstractSocket;

class AcceptPlayRequestCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/accept';
        $this->description = 'Accepts an invitation to play online with an opponent.';
        $this->params = [
            'params' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $params = json_decode(stripslashes($argv[1]), true);

        $gameMode = $socket->getGameModeStorage()->getByHash($params['hash']);

        if (!$gameMode) {
            return $socket->getClientStorage()->sendToOne($id, [
                $this->name => [
                    'mode' => PlayMode::NAME,
                    'message' =>  'This friend request could not be accepted.',
                ],
            ]);
        }

        if ($gameMode->getStatus() === PlayMode::STATUS_PENDING) {
            $decoded = $gameMode->getJwtDecoded();
            $color = (new Color())->opp($decoded->color);
            $decoded->username->{$color} = $params['username'] ?? self::ANONYMOUS_USER;
            $decoded->elo->{$color} = $params['elo'];
            $ids = [...$gameMode->getResourceIds(), $id];
            $gameMode->setJwt((array) $decoded)
                ->setResourceIds($ids)
                ->setStatus(PlayMode::STATUS_ACCEPTED)
                ->setStartedAt(time())
                ->setUpdatedAt(time())
                ->setTimer([
                    Color::W => $decoded->min * 60,
                    Color::B => $decoded->min * 60,
                ]);
            $socket->getGameModeStorage()->set($gameMode);
            if ($decoded->submode === PlayMode::SUBMODE_ONLINE) {
                $socket->getClientStorage()->sendToAll([
                    'broadcast' => [
                        'onlineGames' => $socket->getGameModeStorage()
                            ->decodeByPlayMode(PlayMode::STATUS_PENDING, PlayMode::SUBMODE_ONLINE),
                    ],
                ]);
            }
            return $socket->getClientStorage()->sendToMany($ids, [
                $this->name => [
                    'jwt' => $gameMode->getJwt(),
                    'hash' => hash('adler32', $gameMode->getJwt()),
                    'timer' => $gameMode->getTimer(),
                    'startedAt' => $gameMode->getStartedAt(),
                ],
            ]);
        }
    }
}
