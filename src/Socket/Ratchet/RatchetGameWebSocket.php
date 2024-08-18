<?php

namespace ChessServer\Socket\Ratchet;

use ChessServer\Command\CommandParser;
use ChessServer\Command\Game\LeaveCommand;
use Ratchet\ConnectionInterface;

class RatchetGameWebSocket extends AbstractRatchetWebSocket
{
    public function __construct(CommandParser $parser)
    {
        parent::__construct($parser);
    }

    public function onClose(ConnectionInterface $conn)
    {
        if ($gameMode = $this->gameModeStorage->getById($conn->resourceId)) {
            $this->gameModeStorage->delete($gameMode);
            $this->clientStorage->sendToMany($gameMode->getResourceIds(), [
                '/leave' => [
                    'action' => LeaveCommand::ACTION_ACCEPT,
                ],
            ]);
        }

        $this->clientStorage->detachById($conn->resourceId);

        $this->clientStorage->getLogger()->info('Closed connection', [
            'id' => $conn->resourceId,
            'n' => $this->clientStorage->count()
        ]);
    }
}
