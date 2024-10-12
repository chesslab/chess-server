<?php

namespace ChessServer\Command\Game\Async;

use Chess\Variant\Chess960\FEN\StrToBoard as Chess960FenStrToBoard;
use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Command\AbstractAsyncCommand;
use ChessServer\Command\Game\Game;
use ChessServer\Socket\AbstractSocket;

class RestartCommand extends AbstractAsyncCommand
{
    public function __construct()
    {
        $this->name = '/restart';
        $this->description = 'Restarts an existing game.';
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
        $gameMode = $socket->getGameModeStorage()->getByJwt($params['jwt']);

        $this->pool->add(new RestartTask([
            'decoded' => $gameMode->getJwtDecoded(),
        ]))->then(function ($result) use ($socket, $gameMode) {
            if ($result->variant === Game::VARIANT_960) {
                $startPos = str_split($result->startPos);
                $board = (new Chess960FenStrToBoard($result->fen, $startPos))->create();
                $game = (new Game($result->variant, Game::MODE_PLAY))->setBoard($board);
            } else {
                $game = new Game($result->variant, Game::MODE_PLAY);
            }
            $gameMode->setGame($game)
                ->setJwt((array) $result)
                ->setStartedAt(time())
                ->setUpdatedAt(time())
                ->setTimer([
                    Color::W => $result->min * 60,
                    Color::B => $result->min * 60,
                ]);
            $socket->getGameModeStorage()->set($gameMode);
            return $socket->getClientStorage()->send($gameMode->getResourceIds(), [
                $this->name => [
                    'jwt' => $gameMode->getJwt(),
                    'timer' => $gameMode->getTimer(),
                ],
            ]);
        });
    }
}
