<?php

namespace ChessServer\Command\Game\Async;

use ChessServer\Command\AbstractAsyncCommand;
use ChessServer\Command\Game\Async\UpdateEloTask;
use ChessServer\Command\Game\Mode\PlayMode;
use ChessServer\Socket\AbstractSocket;

class PlayCommand extends AbstractAsyncCommand
{
    public function __construct()
    {
        $this->name = '/play';
        $this->description = 'Plays a move in Portable Game Notation (PGN) format.';
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
        $gameMode = $socket->getGameModeStorage()->getById($id);

        if (get_class($gameMode) === PlayMode::class) {
            $isValid = $gameMode->getGame()->play($params['color'], $params['pgn']);
            if ($isValid) {
                if (isset($gameMode->getGame()->state()->end)) {
                    $this->pool->add(new UpdateEloTask([
                        'result' => $gameMode->getGame()->state()->end['result'],
                        'decoded' => $gameMode->getJwtDecoded(),
                    ]));
                } else {
                    $gameMode->updateTimer($params['color']);
                }
            }
            return $socket->getClientStorage()->send($gameMode->getResourceIds(), [
                $this->name => [
                    ...(array) $gameMode->getGame()->state(),
                    'variant' =>  $gameMode->getGame()->getVariant(),
                    'timer' => $gameMode->getTimer(),
                    'isValid' => $isValid,
                ],
            ]);
        }

        return $socket->getClientStorage()->send(
            $gameMode->getResourceIds(),
            $gameMode->res($params, $this)
        );
    }
}
