<?php

namespace ChessServer\Command;

use ChessServer\Socket;
use ChessServer\GameMode\PlayMode;
use Firebase\JWT\JWT;
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
        if ($gameMode = $socket->getGameModeStorage()->getByHash($argv[1])) {
            if ($gameMode->getState() === PlayMode::STATE_PENDING) {
                $resourceIds = [...$gameMode->getResourceIds(), $from->resourceId];
                $gameMode->setResourceIds($resourceIds)->setState(PlayMode::STATE_ACCEPTED);
                $socket->getGameModeStorage()->set($resourceIds, $gameMode);
                $decoded = JWT::decode($gameMode->getJwt(), $_ENV['JWT_SECRET'], array('HS256'));
                if ($decoded->submode === PlayMode::SUBMODE_ONLINE) {
                    $socket->sendToAll();
                }
                return $socket->sendToMany($resourceIds, [
                    $this->name => [
                        'jwt' => $gameMode->getJwt(),
                        'hash' => md5($gameMode->getJwt()),
                    ],
                ]);
            }
        }

        return $socket->sendToOne($from->resourceId, [
            $this->name => [
                'mode' => PlayMode::NAME,
                'message' =>  'This friend request could not be accepted.',
            ],
        ]);
    }
}
