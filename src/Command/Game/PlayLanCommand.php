<?php

namespace ChessServer\Command\Game;

use ChessServer\Command\AbstractCommand;
use ChessServer\Command\UpdateEloAsyncTask;
use ChessServer\Command\Game\Mode\PlayMode;
use ChessServer\Socket\AbstractSocket;

class PlayLanCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/play_lan';
        $this->description = 'Plays a move in long algebraic notation.';
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
            $isValid = $gameMode->getGame()->playLan($params['color'], $params['lan']);
            if ($isValid) {
                if (isset($gameMode->getGame()->state()->end)) {
                    $this->pool->add(new UpdateEloAsyncTask([
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
