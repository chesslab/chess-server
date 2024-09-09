<?php

namespace ChessServer\Command\Game;

use Chess\SanHeuristic;
use Chess\Function\CompleteFunction;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\FEN\StrToBoard as Chess960FenStrToBoard;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Classical\FEN\StrToBoard as ClassicalFenStrToBoard;
use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\AbstractSocket;

class HeuristicCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/heuristic';
        $this->description = 'Balance of a chess heuristic.';
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

        $function = new CompleteFunction();

        if ($params['variant'] === Chess960Board::VARIANT) {
            $startPos = str_split($params['startPos']);
            $board = isset($params['fen'])
                ? (new Chess960FenStrToBoard($params['fen'], $startPos))->create()
                : new Chess960Board($startPos);
        } elseif ($params['variant'] === ClassicalBoard::VARIANT) {
            $board = isset($params['fen'])
                ? (new ClassicalFenStrToBoard($params['fen']))->create()
                : new ClassicalBoard();
        }

        $balance = (new SanHeuristic($function, $params['name'], $params['movetext'], $board))
            ->getBalance();

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => $balance,
        ]);
    }
}
