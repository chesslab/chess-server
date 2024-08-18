<?php

namespace ChessServer\Command\Game;

use Chess\StandardFunction;
use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\AbstractChesslaBlabSocket;

class EvalNamesCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/eval_names';
        $this->description = 'Evaluation names.';
        $this->params = [
            'settings' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractChesslaBlabSocket $socket, array $argv, int $id)
    {
        $params = json_decode(stripslashes($argv[1]), true);

        if (isset($params['exclude'])) {
            $exclude = explode(',', $params['exclude']);
        } else {
            $exclude = [];
        }

        $exclude = array_map('trim', $exclude);

        $diff = array_diff((new StandardFunction())->names(), $exclude);

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => $diff,
        ]);
    }
}
