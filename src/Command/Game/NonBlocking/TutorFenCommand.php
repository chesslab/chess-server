<?php

namespace ChessServer\Command\Game\NonBlocking;

use Chess\Eval\CompleteFunction;
use Chess\Tutor\FenEvaluation;
use Chess\Variant\Classical\FenToBoardFactory as ClassicalFenToBoardFactory;
use ChessServer\Command\AbstractNonBlockingCommand;
use ChessServer\Socket\AbstractSocket;

class TutorFenCommand extends AbstractNonBlockingCommand
{
    public function __construct()
    {
        $this->name = '/tutor_fen';
        $this->description = 'Explains a FEN position in terms of chess concepts.';
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
        $params = $this->params($argv[1]);

        $board = ClassicalFenToBoardFactory::create($params['fen']);
        $paragraph = (new FenEvaluation(new CompleteFunction(), $board))->paragraph;

        return $socket->getClientStorage()->send([$id], [
            $this->name => implode(' ', $paragraph),
        ]);
    }
}
