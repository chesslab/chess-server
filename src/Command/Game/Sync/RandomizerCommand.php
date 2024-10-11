<?php

namespace ChessServer\Command\Game\Sync;

use Chess\Randomizer\Randomizer;
use Chess\Randomizer\Checkmate\TwoBishopsRandomizer;
use Chess\Randomizer\Endgame\PawnEndgameRandomizer;
use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Command\AbstractSyncCommand;
use ChessServer\Socket\AbstractSocket;

class RandomizerCommand extends AbstractSyncCommand
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
            'params' => '<string>',
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
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $params = json_decode(stripslashes($argv[1]), true);

        try {
            if (count($params['items']) === 1) {
                $color = array_key_first($params['items']);
                $pieceIds = str_split(current($params['items']));
                if ($pieceIds === ['B', 'B']) {
                    $board = (new TwoBishopsRandomizer($params['turn']))->board;
                } elseif ($pieceIds === ['P']) {
                    $board = (new PawnEndgameRandomizer($params['turn']))->board;
                } else {
                    $board = (new Randomizer($params['turn'], [$color => $pieceIds]))->board;
                }
            } else {
                $wIds = str_split($params['items'][Color::W]);
                $bIds = str_split($params['items'][Color::B]);
                $board = (new Randomizer($params['turn'], [
                    Color::W => $wIds,
                    Color::B => $bIds,
                ]))->board;
            }
            return $socket->getClientStorage()->send([$id], [
                $this->name => [
                    'turn' => $board->turn,
                    'fen' => $board->toFen(),
                ],
            ]);
        } catch (\Throwable $e) {
            return $socket->getClientStorage()->send([$id], [
                $this->name => [
                    'message' => 'A random puzzle could not be loaded.',
                ],
            ]);
        }
    }
}
