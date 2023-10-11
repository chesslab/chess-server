<?php

namespace ChessServer\Command;

use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Socket\ChesslaBlab;
use ChessServer\Exception\InternalErrorException;
use ChessServer\Game\PlayMode;

class AcceptPlayRequestCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/accept';
        $this->description = 'Accepts an invitation to play online with an opponent.';
        $this->params = [
            'jwt' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(ChesslaBlab $socket, array $argv, int $resourceId)
    {
        $gameMode = $socket->getGameModeStorage()->getByHash($argv[1]);

        if (!$gameMode) {
            return $socket->sendToOne($resourceId, [
                $this->name => [
                    'mode' => PlayMode::NAME,
                    'message' =>  'This friend request could not be accepted.',
                ],
            ]);
        }

        if ($gameMode->getStatus() === PlayMode::STATUS_PENDING) {
            $decoded = $gameMode->getJwtDecoded();
            $resourceIds = [...$gameMode->getResourceIds(), $resourceId];
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
