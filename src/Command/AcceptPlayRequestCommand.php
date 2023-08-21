<?php

namespace ChessServer\Command;

use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Socket;
use ChessServer\Exception\InternalErrorException;
use ChessServer\GameMode\PlayMode;
use Ratchet\ConnectionInterface;

class AcceptPlayRequestCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/accept';
        $this->description = 'Accepts a request to play a game.';
        $this->params = [
            'jwt' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(Socket $socket, array $argv, ConnectionInterface $from)
    {
        $gameMode = $socket->getGameModeStorage()->getByHash($argv[1]);

        if (!$gameMode) {
            return $socket->sendToOne($from->resourceId, [
                $this->name => [
                    'mode' => PlayMode::NAME,
                    'message' =>  'This friend request could not be accepted.',
                ],
            ]);
        }

        if ($gameMode->getStatus() === PlayMode::STATUS_PENDING) {
            $decoded = $gameMode->getJwtDecoded();
            $resourceIds = [...$gameMode->getResourceIds(), $from->resourceId];
            $gameMode->setResourceIds($resourceIds)
                ->setStatus(PlayMode::STATUS_ACCEPTED)
                ->setStartedAt(time())
                ->setUpdatedAt(time())
                ->setTimer([
                    Color::W => $decoded->min * 60,
                    Color::B => $decoded->min * 60,
                ]);
            $socket->getGameModeStorage()->set($gameMode);
            if ($decoded->submode === PlayMode::SUBMODE_ONLINE) {
                $socket->sendToAll();
            }
            return $socket->sendToMany($resourceIds, [
                $this->name => [
                    'jwt' => $gameMode->getJwt(),
                    'hash' => md5($gameMode->getJwt()),
                    'timer' => $gameMode->getTimer(),
                    'startedAt' => $gameMode->getStartedAt(),
                ],
            ]);
        }
    }
}
