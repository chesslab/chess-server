<?php

namespace ChessServer\Command\Game\Blocking;

use Chess\Variant\VariantType;
use Chess\Variant\CapablancaFischer\FenToBoardFactory as CapablancaFischerFenToBoardFactory;
use Chess\Variant\Chess960\FenToBoardFactory as Chess960FenToBoardFactory;
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
                $board = Chess960FenToBoardFactory::create($result->fen);
                $game = (new Game($result->variant, Game::MODE_PLAY))->setBoard($board);
            } elseif ($result->variant === VariantType::CAPABLANCA_FISCHER) {
                $board = CapablancaFischerFenToBoardFactory::create($result->fen);
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
