<?php

namespace ChessServer\Command;

use ChessServer\Exception\InternalErrorException;
use ChessServer\Game\PlayMode;
use ChessServer\Socket\ChesslaBlabSocket;

class RematchCommand extends AbstractCommand
{
    const ACTION_ACCEPT    = 'accept';

    const ACTION_DECLINE   = 'decline';

    const ACTION_PROPOSE   = 'propose';

    public function __construct()
    {
        $this->name = '/rematch';
        $this->description = 'Allows to offer a rematch.';
        $this->params = [
            // mandatory param
            'action' => [
                self::ACTION_ACCEPT,
                self::ACTION_DECLINE,
                self::ACTION_PROPOSE,
            ],
        ];
    }

    public function validate(array $argv)
    {
        if (in_array($argv[1], $this->params['action'])) {
            return count($argv) - 1 === count($this->params);
        }

        return false;
    }

    public function run(ChesslaBlabSocket $socket, array $argv, int $resourceId)
    {
        $gameMode = $socket->getGameModeStorage()->getByResourceId($resourceId);

        if (!$gameMode) {
            throw new InternalErrorException();
        }

        if (is_a($gameMode, PlayMode::class)) {
            return $socket->getClientStorage()->sendToMany($gameMode->getResourceIds(), [
                $this->name => [
                    'action' => $argv[1],
                ],
            ]);
        }
    }
}
