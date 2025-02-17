<?php

namespace ChessServer\Command\Game\Blocking;

use Chess\FenToBoardFactory;
use Chess\Variant\VariantType;
use Chess\Variant\CapablancaFischer\Board as CapablancaFischerBoard;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Classical\PGN\Color;
use ChessServer\Command\AbstractBlockingCommand;
use ChessServer\Command\Game\Game;
use ChessServer\Socket\AbstractSocket;

class RestartCommand extends AbstractBlockingCommand
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
        $params = $this->params($argv[1]);
        $gameMode = $socket->getGameModeStorage()->getByJwt($params['jwt']);

        $this->pool->add(new RestartTask([
            'decoded' => $gameMode->getJwtDecoded(),
        ]))->then(function ($result) use ($socket, $gameMode) {
            if ($result->variant === VariantType::CHESS_960) {
                $shuffle = str_split($result->shuffle);
                $board = FenToBoardFactory::create($result->fen, new Chess960Board($shuffle));
                $game = (new Game($result->variant, Game::MODE_PLAY))->setBoard($board);
            } elseif ($result->variant === VariantType::CAPABLANCA_FISCHER) {
                $shuffle = str_split($result->shuffle);
                $board = FenToBoardFactory::create($result->fen, new CapablancaFischerBoard($shuffle));
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
