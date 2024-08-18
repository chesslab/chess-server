<?php

namespace ChessServer\Socket;

use ChessServer\Command\CommandParser;
use ChessServer\Command\Game\LeaveCommand;

class WorkermanGameWebSocket extends AbstractWorkermanWebSocket
{
    public function __construct(string $socketName, array $context, CommandParser $parser)
    {
        parent::__construct($socketName, $context, $parser);

        $this->connect()->message()->error()->close();
    }

    protected function close()
    {
        $this->worker->onClose = function ($conn) {
            if ($gameMode = $this->gameModeStorage->getById($conn->id)) {
                $this->gameModeStorage->delete($gameMode);
                $this->clientStorage->sendToMany($gameMode->getResourceIds(), [
                    '/leave' => [
                        'action' => LeaveCommand::ACTION_ACCEPT,
                    ],
                ]);
            }

            $this->clientStorage->detachById($conn->id);

            $this->clientStorage->getLogger()->info('Closed connection', [
                'id' => $conn->id,
                'n' => $this->clientStorage->count()
            ]);
        };

        return $this;
    }
}
