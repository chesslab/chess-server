<?php

namespace ChessServer\Command;

use ChessServer\Socket;
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
        if ($gameMode = $socket->getGameModeByHash($argv[1])) {
            $gameMode->setState(PlayMode::STATE_ACCEPTED);
            if ($socket->syncGameModeWith($gameMode, $from)) {
                $jwt = $gameMode->getJwt();
                return $socket->sendToMany($gameMode->getResourceIds(), [
                    $this->name => [
                        'jwt' => $jwt,
                        'hash' => md5($jwt),
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
