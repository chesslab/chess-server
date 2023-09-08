<?php

namespace ChessServer\Command;

use ChessServer\Socket\ChesslaBlab;
use ChessServer\Exception\InternalErrorException;
use ChessServer\Game\PlayMode;

class StockfishCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/stockfish';
        $this->description = "Returns Stockfish's response to the current position.";
        $this->params = [
            // mandatory param
            'options' => [
                'Skill Level' => 'int',
            ],
            // mandatory param
            'params' => [
                'depth' => 'int',
            ],
        ];
    }

    public function validate(array $argv)
    {
        isset($argv[1]) ? $options = json_decode(stripslashes($argv[1]), true) : $options = null;
        isset($argv[2]) ? $params = json_decode(stripslashes($argv[2]), true) : $params = null;

        if ($options) {
            foreach ($options as $key => $val) {
                if (
                    !in_array($key, array_keys($this->params['options'])) ||
                    !is_int($val)
                ) {
                    return false;
                }
            }
        } else {
            return false;
        }

        if ($params) {
            foreach ($params as $key => $val) {
                if (
                    !in_array($key, array_keys($this->params['params'])) ||
                    !is_int($val)
                ) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    public function run(ChesslaBlab $socket, array $argv, int $resourceId)
    {
        $gameMode = $socket->getGameModeStorage()->getByResourceId($resourceId);

        if (!$gameMode) {
            throw new InternalErrorException();
        }

        return $socket->sendToOne(
            $resourceId,
            $gameMode->res($argv, $this)
        );
    }
}
