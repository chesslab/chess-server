<?php

namespace ChessServer\Command;

use ChessServer\Socket\ChesslaBlab;
use ChessServer\Exception\InternalErrorException;
use ChessServer\Game\PlayMode;

class TakebackCommand extends AbstractCommand
{
    const ACTION_ACCEPT    = 'accept';

    const ACTION_DECLINE   = 'decline';

    const ACTION_PROPOSE   = 'propose';

    public function __construct()
    {
        $this->name = '/takeback';
        $this->description = 'Allows to manage a takeback.';
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

    public function run(ChesslaBlab $socket, array $argv, int $resourceId)
    {
        $gameMode = $socket->getGameModeStorage()->getByResourceId($resourceId);

        if (!$gameMode) {
            throw new InternalErrorException();
        }

        if (is_a($gameMode, PlayMode::class)) {
            return $socket->sendToMany(
                $gameMode->getResourceIds(),
                $gameMode->res($argv, $this)
            );
        }
    }
}
