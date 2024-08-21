<?php

namespace ChessServer\Socket\Workerman;

use Chess\Computer\GrandmasterMove;
use ChessServer\Command\Parser;
use ChessServer\Command\Game\GameModeStorage;
use ChessServer\Command\Game\LeaveCommand;

class GameWebSocket extends AbstractWebSocket
{
    protected GrandmasterMove $gmMove;

    protected GameModeStorage $gameModeStorage;

    public function __construct(string $socketName, array $context, Parser $parser)
    {
        parent::__construct($socketName, $context, $parser);

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
                $this->clientStorage->sendToMany($gameMode->getResourceIds(), [
                    '/leave' => [
                        'action' => LeaveCommand::ACTION_ACCEPT,
                    ],
                ]);
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
