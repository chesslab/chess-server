<?php

namespace ChessServer\Command\Game\Async;

use Chess\Computer\GrandmasterMove;
use Chess\UciEngine\UciEngine;
use Chess\UciEngine\Details\Limit;
use Chess\Variant\Classical\Board;
use ChessServer\Socket\AbstractSocket;
use Spatie\Async\Task;

class StockfishTask extends Task
{
    private array $params;

    private Board $board;

    private GrandmasterMove $gmMove;

    public function __construct($params, $board)
    {
        $this->params = $params;
        $this->board = $board;
    }

    public function configure()
    {
        $this->gmMove = new GrandmasterMove(AbstractSocket::DATA_FOLDER.'/players.json');
    }

    public function run()
    {
        if ($move = $this->gmMove->move($this->board)) {
            return $move;
        }

        $limit = new Limit();
        $limit->depth = $this->params['params']['depth'];
        $stockfish = (new UciEngine('/usr/games/stockfish'))
            ->setOption('Skill Level', $this->params['options']['Skill Level']);
        $analysis = $stockfish->analysis($this->board, $limit);

        $clone = $this->board->clone();
        $clone->playLan($this->board->turn, $analysis['bestmove']);
        $history = $clone->history;
        $end = end($history);

        return [
            'pgn' => $end['move']['pgn'],
        ];
    }
}
