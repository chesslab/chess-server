<?php

namespace ChessServer\Socket\Ratchet;

use Chess\Computer\GrandmasterMove;
use ChessServer\Command\Parser;
use ChessServer\Command\Game\GameModeStorage;
use ChessServer\Socket\DbReconnectTrait;
use Ratchet\ConnectionInterface;

class GameWebSocket extends AbstractWebSocket
{
    use DbReconnectTrait;

    public function __construct(Parser $parser)
    {
        parent::__construct($parser);

        $this->loop->addPeriodicTimer($this->timeInterval, function() {
            $this->reconnect();
        });

        $this->gmMove = new GrandmasterMove(self::DATA_FOLDER.'/players.json');
        $this->gameModeStorage = new GameModeStorage();
    }

    public function getGmMove(): GrandmasterMove
    {
        return $this->gmMove;
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
