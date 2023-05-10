<?php

namespace ChessServer\Command;

use Chess\Game;
use ChessServer\Socket;
use ChessServer\GameMode\PlayMode;
use Firebase\JWT\JWT;
use Ratchet\ConnectionInterface;

class RestartCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/restart';
        $this->description = 'Restarts a game.';
        $this->params = [
            'hash' => '<string>',
        ];
        $this->dependsOn = [];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(Socket $socket, array $argv, ConnectionInterface $from)
    {
        if ($gameMode = $socket->gameModeByHash($argv[1])) {
            $jwt = $gameMode->getJwt();
            $decoded = JWT::decode($jwt, $_ENV['JWT_SECRET'], array('HS256'));
            $decoded->iat = time();
            $decoded->exp = time() + 3600; // one hour by default
            $newJwt = JWT::encode($decoded, $_ENV['JWT_SECRET']);
            $resourceIds = $gameMode->getResourceIds();
            $newGameMode = new PlayMode(
                new Game(Game::VARIANT_CLASSICAL, Game::MODE_PLAY),
                [$resourceIds[0], $resourceIds[1]],
                $newJwt
            );
            $newGameMode->setState(PlayMode::STATE_ACCEPTED);
            $socket->setGameModes([$resourceIds[0], $resourceIds[1]], $newGameMode);

            return $socket->sendToMany($newGameMode->getResourceIds(), [
                $this->name => [
                    'jwt' => $newJwt,
                    'hash' => md5($newJwt),
                ],
            ]);
        }
    }
}
