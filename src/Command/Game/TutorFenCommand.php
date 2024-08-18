<?php

namespace ChessServer\Command\Game;

use Chess\FenToBoardFactory;
use Chess\Tutor\FenEvaluation;
use Chess\Variant\Classical\Board;
use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\AbstractChesslaBlabSocket;

class TutorFenCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/tutor_fen';
        $this->description = 'Explains a FEN position in terms of chess concepts.';
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

        $board = FenToBoardFactory::create($params['fen'], new Board());

        $paragraph = (new FenEvaluation($board))->paragraph;

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => implode(' ', $paragraph),
        ]);
    }
}
