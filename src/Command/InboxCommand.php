<?php

namespace ChessServer\Command;

use Chess\Game;
use Chess\Movetext;
use Chess\Exception\MovetextException;
use Chess\Variant\Capablanca80\Board as Capablanca80Board;
use Chess\Variant\Capablanca80\FEN\StrToBoard as Capablanca80FenStrToBoard;
use Chess\Variant\Capablanca80\PGN\Move as Capablanca80PgnMove;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\StartPosition;
use Chess\Variant\Chess960\FEN\StrToBoard as Chess960FenStrToBoard;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Classical\FEN\StrToBoard as ClassicalFenStrToBoard;
use Chess\Variant\Classical\PGN\Move as ClassicalPgnMove;
use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Socket;
use ChessServer\GameMode\PlayMode;
use Ratchet\ConnectionInterface;

class InboxCommand extends AbstractCommand
{
    const ACTION_CREATE = 'create';

    const ACTION_READ = 'read';

    const ACTION_REPLY = 'reply';

    public function __construct()
    {
        $this->name = '/inbox';
        $this->description = "Correspondence chess.";
        $this->params = [
            // mandatory
            'action' => [
                self::ACTION_CREATE,
                self::ACTION_READ,
                self::ACTION_REPLY,
            ],
            // optional
            'variant' => [
                Game::VARIANT_960,
                Game::VARIANT_CAPABLANCA_80,
                Game::VARIANT_CAPABLANCA_100,
                Game::VARIANT_CLASSICAL,
            ],
            // optional
            'settings' => [
                'fen' => '<string>',
                'movetext' => '<string>',
                'startPos' => '<string>',
            ],
            // optional
            'hash' => '<string>',
            // optional
            'movetext' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        if (in_array($argv[1], $this->params['action'])) {
            return count($argv) - 1 === count($this->params) - 2 ||
                count($argv) - 1 === count($this->params) - 3;
        }

        return false;
    }

    public function run(Socket $socket, array $argv, ConnectionInterface $from)
    {
        if (InboxCommand::ACTION_CREATE === $argv[1]) {
            $hash = md5(uniqid());
            $settings = json_decode(stripslashes($argv[3]), true);
            try {
                if ($argv[2] === Game::VARIANT_960) {
                    $startPos = str_split($settings['startPos']);
                    $fen = $settings['fen'] ?? (new Chess960Board($startPos))->toFen();
                    $board = (new Chess960FenStrToBoard($fen, $startPos))->create();
                } elseif ($argv[2] === Game::VARIANT_CAPABLANCA_80) {
                    $fen = $settings['fen'] ?? (new Capablanca80Board())->toFen();
                    $board = (new Capablanca80FenStrToBoard($fen))->create();
                } else {
                    $fen = $settings['fen'] ?? (new ClassicalBoard())->toFen();
                    $board = (new ClassicalFenStrToBoard($fen))->create();
                }
            } catch (\Exception $e) {
                return $socket->sendToOne($from->resourceId, [
                    $this->name => [
                        'action' => InboxCommand::ACTION_CREATE,
                        'message' =>  'Invalid FEN, please try again with a different one.',
                    ],
                ]);
            }
            $inbox = [
                'hash' => $hash,
                'variant' => $argv[2],
                'settings' => $settings,
                'fen' => $board->toFen(),
                'movetext' => '',
                'createdAt' => (new \DateTime())->format('Y-m-d H:i:s'),
                'createdBy' => $from->resourceId,
            ];
            $socket->getInboxStore()->insert($inbox);
            return $socket->sendToOne($from->resourceId, [
                $this->name => [
                    'action' => InboxCommand::ACTION_CREATE,
                    'hash' => $hash,
                    'inbox' =>  $inbox,
                ],
            ]);
        } elseif (InboxCommand::ACTION_READ === $argv[1]) {
            if ($inbox = $socket->getInboxStore()->findOneBy(['hash', '=', $argv[2]])) {
                return $socket->sendToOne($from->resourceId, [
                    $this->name => [
                        'action' => InboxCommand::ACTION_READ,
                        'inbox' => $inbox,
                    ],
                ]);
            } else {
                return $socket->sendToOne($from->resourceId, [
                    $this->name => [
                        'action' => InboxCommand::ACTION_READ,
                        'message' =>  'This inbox code does not exist.',
                    ],
                ]);
            }
        } elseif (InboxCommand::ACTION_REPLY === $argv[1]) {
            if ($inbox = $socket->getInboxStore()->findOneBy(['hash', '=', $argv[2]])) {
                if (isset($inbox['settings']['fen'])) {
                    if ($inbox['variant'] === Game::VARIANT_960) {
                        $move = new ClassicalPgnMove();
                        $startPos = str_split($inbox['settings']['startPos']);
                        $board = (new Chess960FenStrToBoard($inbox['settings']['fen'], $startPos))
                            ->create();
                    } elseif ($inbox['variant'] === Game::VARIANT_CAPABLANCA_80) {
                        $move = new Capablanca80PgnMove();
                        $board = (new Capablanca80FenStrToBoard($inbox['settings']['fen']))
                            ->create();
                    } else {
                        $move = new ClassicalPgnMove();
                        $board = (new ClassicalFenStrToBoard($inbox['settings']['fen']))
                            ->create();
                    }
                } else {
                    if ($inbox['variant'] === Game::VARIANT_960) {
                        $move = new ClassicalPgnMove();
                        $startPos = (new StartPosition())->create();
                        $board = new Chess960Board($startPos);
                    } elseif ($inbox['variant'] === Game::VARIANT_CAPABLANCA_80) {
                        $move = new Capablanca80PgnMove();
                        $board = new Capablanca80Board();
                    } else {
                        $move = new ClassicalPgnMove();
                        $board = new ClassicalBoard();
                    }
                }
                try {
                    if ($inbox['movetext']) {
                        $movetext = new Movetext($move, $inbox['movetext']);
                        $movetext->validate();
                        foreach ($movetext->getMovetext()->moves as $key => $val) {
                            $board->play($board->getTurn(), $val);
                        }
                    }
                    if (!$board->play($board->getTurn(), $argv[3])) {
                        throw new MovetextException();
                    }
                    $inbox['fen'] = $board->toFen();
                    $inbox['movetext'] = $board->getMovetext();
                    $inbox['updatedAt'] = (new \DateTime())->format('Y-m-d H:i:s');
                    $inbox['updatedBy'] = $from->resourceId;
                    $socket->getInboxStore()->update($inbox);
                    return $socket->sendToOne($from->resourceId, [
                        $this->name => [
                            'action' => InboxCommand::ACTION_REPLY,
                            'message' =>  'Chess move successfully sent.',
                        ],
                    ]);
                } catch (\Exception $e) {
                    return $socket->sendToOne($from->resourceId, [
                        $this->name => [
                            'action' => InboxCommand::ACTION_REPLY,
                            'message' =>  'Invalid PGN move, please try again with a different one.',
                        ],
                    ]);
                }
            }
        }
    }
}
