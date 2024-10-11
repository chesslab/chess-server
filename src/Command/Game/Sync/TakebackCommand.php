<?php

namespace ChessServer\Command\Game\Sync;

use ChessServer\Command\AbstractSyncCommand;
use ChessServer\Socket\AbstractSocket;

class TakebackCommand extends AbstractSyncCommand
{
    const ACTION_ACCEPT    = 'accept';
    const ACTION_DECLINE   = 'decline';
    const ACTION_PROPOSE   = 'propose';

    public function __construct()
    {
        $this->name = '/takeback';
        $this->description = 'Takes back a move.';
        $this->params = [
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

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $gameMode = $socket->getGameModeStorage()->getById($id);

        if ($argv[1] === self::ACTION_PROPOSE) {
            $diff = array_diff($gameMode->getResourceIds(), [$id]);
            return $socket->getClientStorage()->send($diff, [
                $this->name => [
                    'action' => self::ACTION_PROPOSE,
                ],
            ]);
        }

        return $socket->getClientStorage()->send($gameMode->getResourceIds(), [
            $this->name => [
                'action' => $argv[1],
            ],
        ]);
    }
}
