<?php

namespace ChessServer\Socket\Ratchet;

use ChessServer\Command\Parser;
use ChessServer\Command\Game\GameModeStorage;
use Ratchet\ConnectionInterface;

class GameWebSocket extends AbstractWebSocket
{
    public function __construct(Parser $parser)
    {
        parent::__construct($parser);

        $this->gameModeStorage = new GameModeStorage();
    }

    public function getGameModeStorage(): GameModeStorage
    {
        return $this->gameModeStorage;
    }

    public function onClose(ConnectionInterface $conn)
    {
        if ($gameMode = $this->gameModeStorage->getById($conn->resourceId)) {
            $this->gameModeStorage->delete($gameMode);
        }
        $this->clientStorage->detachById($conn->resourceId);
        $this->clientStorage->getLogger()->info('Closed connection', [
            'id' => $conn->resourceId,
            'n' => $this->clientStorage->count(),
        ]);
    }
}
