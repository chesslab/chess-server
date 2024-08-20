<?php

namespace ChessServer\Command\Game;

use Chess\FenToBoardFactory;
use Chess\Play\RavPlay;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Classical\Board as ClassicalBoard;
use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\AbstractSocket;

class PlayRavCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/play_rav';
        $this->description = 'Plays the moves in a RAV movetext.';
        $this->params = [
            'settings' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $params = json_decode(stripslashes($argv[1]), true);

        if ($params['variant'] === Chess960Board::VARIANT) {
            $startPos = str_split($params['startPos']);
            $board = new Chess960Board($startPos);
            if (isset($params['fen'])) {
                $board = FenToBoardFactory::create($params['fen'], $board);
            }
            $ravPlay = new RavPlay($params['movetext'], $board);
        } else {
            $board = new ClassicalBoard();
            if (isset($params['fen'])) {
                $board = FenToBoardFactory::create($params['fen'], $board);
            }
            $ravPlay = new RavPlay($params['movetext'], $board);
        }

        $board = $ravPlay->validate()->board;

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => [
                'variant' => $params['variant'],
                'turn' => $board->turn,
                'filtered' => $ravPlay->ravMovetext->filtered(),
                'movetext' => $ravPlay->ravMovetext->main(),
                'breakdown' => $ravPlay->ravMovetext->breakdown,
                'fen' => $ravPlay->fen,
                ...($params['variant'] === Chess960Board::VARIANT
                    ? ['startPos' =>  $params['startPos']]
                    : []
                ),
            ],
        ]);
    }
}
