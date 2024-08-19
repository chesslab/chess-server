<?php

namespace ChessServer\Socket\Ratchet;

use ChessServer\Command\Parser;
use ChessServer\Command\Game\LeaveCommand;
use Ratchet\ConnectionInterface;

class GameWebSocket extends AbstractWebSocket
{
    public function __construct(Parser $parser)
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
