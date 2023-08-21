<?php

namespace ChessServer\Command;

use ChessServer\Socket;
use ChessServer\Exception\InternalErrorException;
use ChessServer\GameMode\PlayMode;
use Ratchet\ConnectionInterface;

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

    public function run(Socket $socket, array $argv, ConnectionInterface $from)
    {
        $gameMode = $socket->getGameModeStorage()->getByResourceId($from->resourceId);

        if (!$gameMode) {
            throw new InternalErrorException();
        }

        return $socket->sendToOne(
            $from->resourceId,
            $gameMode->res($argv, $this)
        );
    }
}
