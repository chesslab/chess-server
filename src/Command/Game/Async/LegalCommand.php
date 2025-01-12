<?php

namespace ChessServer\Command\Game\Async
;

use ChessServer\Command\AbstractAsyncCommand;
use ChessServer\Socket\AbstractSocket;

class LegalCommand extends AbstractAsyncCommand
{
    public function __construct()
    {
        $this->name = '/legal';
        $this->description = 'Returns the legal FEN positions of a piece.';
        $this->params = [
            'params' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $params = json_decode(stripslashes($argv[1]), true);
        $gameMode = $socket->getGameModeStorage()->getById($id);

        return $socket->getClientStorage()->send([$id],
            $gameMode->res($params, $this)
        );
    }
}
