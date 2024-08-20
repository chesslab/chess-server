<?php

namespace ChessServer\Command\Game;

use Chess\Variant\Chess960\FEN\StrToBoard as Chess960FenStrToBoard;
use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Command\AbstractCommand;
use ChessServer\Command\Game\Game;
use ChessServer\Command\Game\Mode\PlayMode;
use ChessServer\Socket\AbstractSocket;
use Firebase\JWT\JWT;

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

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        if ($gameMode = $socket->getGameModeStorage()->getByHash($argv[1])) {
            $decoded = $gameMode->getJwtDecoded();
            $decoded->iat = time();
            $decoded->exp = time() + 3600; // one hour by default
            if ($decoded->variant === Game::VARIANT_960) {
                $startPos = str_split($decoded->startPos);
                $board = (new Chess960FenStrToBoard($decoded->fen, $startPos))->create();
                $game = (new Game($decoded->variant, Game::MODE_PLAY))->setBoard($board);
            } else {
                $game = new Game($decoded->variant, Game::MODE_PLAY);
            }
            $newJwt = JWT::encode((array)$decoded, $_ENV['JWT_SECRET'], 'HS256');
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

            return $socket->getClientStorage()->sendToMany($newGameMode->getResourceIds(), [
                $this->name => [
                    'jwt' => $newJwt,
                    'hash' => hash('adler32', $newJwt),
                    'timer' => $newGameMode->getTimer(),
                ],
            ]);
        }
    }
}
