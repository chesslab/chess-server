<?php

namespace ChessServer\Command;

use Chess\Variant\Classical\FEN\BoardToStr;
use Chess\Variant\Classical\PGN\AN\Color;
use Chess\Variant\Classical\Randomizer\Randomizer;
use Chess\Variant\Classical\Randomizer\Checkmate\TwoBishopsRandomizer;
use Chess\Variant\Classical\Randomizer\Endgame\PawnEndgameRandomizer;
use ChessServer\Socket\ChessSocket;
use ChessServer\GameMode\PlayMode;
use Ratchet\ConnectionInterface;

class RandomizerCommand extends AbstractCommand
{
    const TYPE_P    = 'P';

    const TYPE_Q    = 'Q';

    const TYPE_R    = 'R';

    const TYPE_BB   = 'BB';

    const TYPE_BN   = 'BN';

    const TYPE_QR   = 'QR';

    public function __construct()
    {
        $this->name = '/randomizer';
        $this->description = 'Starts a random position.';
        $this->params = [
            // mandatory param
            'turn' => '<string>',
            // mandatory param
            'items' => '<string>',
        ];
    }

    public function cases()
    {
        return [
            self::TYPE_P,
            self::TYPE_Q,
            self::TYPE_R,
            self::TYPE_BB,
            self::TYPE_BN,
            self::TYPE_QR,
        ];
    }

    public function validate(array $argv)
    {
        isset($argv[1]) ? $turn = $argv[1] : $turn = null;
        isset($argv[2]) ? $items = json_decode(stripslashes($argv[2]), true) : $items = null;

        if ($turn !== Color::W && $turn !== Color::B) {
            return false;
        }

        if ($items) {
            $color = array_key_first($items);
            if ($color !== Color::W && $color !== Color::B) {
                return false;
            }
            $ids = current($items);
            if (!in_array($ids, self::cases())) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    public function run(ChessSocket $socket, array $argv, ConnectionInterface $from)
    {
        try {
            $items = json_decode(stripslashes($argv[2]), true);
            if (count($items) === 1) {
                $color = array_key_first($items);
                $ids = str_split(current($items));
                if ($ids === ['B', 'B']) {
                    $board = (new TwoBishopsRandomizer($argv[1]))->getBoard();
                } elseif ($ids === ['P']) {
                    $board = (new PawnEndgameRandomizer($argv[1]))->getBoard();
                } else {
                    $board = (new Randomizer($argv[1], [$color => $ids]))->getBoard();
                }
            } else {
                $wIds = str_split($items[Color::W]);
                $bIds = str_split($items[Color::B]);
                $board = (new Randomizer($argv[1], [
                    Color::W => $wIds,
                    Color::B => $bIds,
                ]))->getBoard();
            }
            return $socket->sendToOne($from->resourceId, [
                $this->name => [
                    'turn' => $board->getTurn(),
                    'fen' => (new BoardToStr($board))->create(),
                ],
            ]);
        } catch (\Throwable $e) {
            return $socket->sendToOne($from->resourceId, [
                $this->name => [
                    'message' => 'A random puzzle could not be loaded.',
                ],
            ]);
        }
    }
}
