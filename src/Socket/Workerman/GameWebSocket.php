<?php

namespace ChessServer\Socket\Workerman;

use Chess\Computer\GrandmasterMove;
use ChessServer\Command\Parser;
use ChessServer\Command\Game\GameModeStorage;
use ChessServer\Socket\DbReconnectTrait;
use Workerman\Timer;

class GameWebSocket extends AbstractWebSocket
{
    use DbReconnectTrait;

    private GrandmasterMove $gmMove;

    private GameModeStorage $gameModeStorage;

    public function __construct(string $socketName, array $context, Parser $parser)
    {
        parent::__construct($socketName, $context, $parser);

        $this->worker->onWorkerStart = function() {
            Timer::add($this->timeInterval, function() {
                $this->reconnect();
            });
        };

        $this->gmMove = new GrandmasterMove(self::DATA_FOLDER.'/players.json');
        $this->gameModeStorage = new GameModeStorage();

        $this->connect()->message()->error()->close();
    }

    public function getGmMove(): GrandmasterMove
    {
        return $this->gmMove;
    }

    public function getGameModeStorage(): GameModeStorage
    {
        return $this->gameModeStorage;
    }

    protected function close()
    {
        $this->worker->onClose = function ($conn) {
            if ($gameMode = $this->gameModeStorage->getById($conn->id)) {
                $this->gameModeStorage->delete($gameMode);
            }
            $this->clientStorage->detachById($conn->id);
            $this->clientStorage->getLogger()->info('Closed connection', [
                'id' => $conn->id,
                'n' => $this->clientStorage->count(),
            ]);
        };

        return $this;
    }
}
