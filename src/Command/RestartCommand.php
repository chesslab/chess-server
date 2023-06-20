<?php

namespace ChessServer\Command;

use Chess\Variant\Chess960\FEN\StrToBoard as Chess960FenStrToBoard;
use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Game;
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
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(Socket $socket, array $argv, ConnectionInterface $from)
    {
        if ($gameMode = $socket->getGameModeStorage()->getByHash($argv[1])) {
            $decoded = $gameMode->getJwtDecoded();
            $decoded->iat = time();
            $decoded->exp = time() + 3600; // one hour by default
            if ($decoded->variant === Game::VARIANT_960) {
                $startPos = str_split($decoded->startPos);
                $board = (new Chess960FenStrToBoard($decoded->fen, $startPos))->create();
                $game = (new Game($decoded->variant, Game::MODE_PLAY))->setBoard($board);
            } else if ($decoded->variant === Game::VARIANT_CAPABLANCA) {
                $game = new Game($decoded->variant, Game::MODE_PLAY);
            } else {
                $game = new Game($decoded->variant, Game::MODE_PLAY);
            }
            $newJwt = JWT::encode($decoded, $_ENV['JWT_SECRET']);
            $newGameMode = new PlayMode(
                $game,
                $gameMode->getResourceIds(),
                $newJwt
            );
            $newGameMode->setStatus(PlayMode::STATUS_ACCEPTED)
                ->setStartedAt(time())
                ->setUpdatedAt(time())
                ->setTimer([
                    Color::W => $decoded->min * 60,
                    Color::B => $decoded->min * 60,
                ]);
            $socket->getGameModeStorage()->set($newGameMode);

            return $socket->sendToMany($newGameMode->getResourceIds(), [
                $this->name => [
                    'jwt' => $newJwt,
                    'hash' => md5($newJwt),
                    'timer' => $newGameMode->getTimer(),
                ],
            ]);
        }
    }
}
