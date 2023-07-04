<?php

namespace ChessServer\Command;

use Chess\Play\RavPlay;
use Chess\Play\SanPlay;
use Chess\Variant\Capablanca\Board as CapablancaBoard;
use Chess\Variant\Capablanca\FEN\StrToBoard as CapablancaFenStrToBoard;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\StartPosition;
use Chess\Variant\Chess960\FEN\StrToBoard as Chess960FenStrToBoard;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Classical\FEN\StrToBoard as ClassicalFenStrToBoard;
use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Game;
use ChessServer\Socket;
use ChessServer\GameMode\FenMode;
use ChessServer\GameMode\GmMode;
use ChessServer\GameMode\PlayMode;
use ChessServer\GameMode\RavMode;
use ChessServer\GameMode\SanMode;
use ChessServer\GameMode\StockfishMode;
use Firebase\JWT\JWT;
use Ratchet\ConnectionInterface;

class StartCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/start';
        $this->description = 'Starts a new game.';
        $this->params = [
            // mandatory param
            'variant' => [
                Game::VARIANT_960,
                Game::VARIANT_CAPABLANCA,
                Game::VARIANT_CLASSICAL,
            ],
            // mandatory param
            'mode' => [
                GmMode::NAME,
                FenMode::NAME,
                SanMode::NAME,
                RavMode::NAME,
                PlayMode::NAME,
                StockfishMode::NAME,
            ],
            // additional param
            'settings' => [
                'color' => [
                    Color::W,
                    Color::B,
                ],
                'fen' => '<string>',
                'movetext' => '<string>',
                'settings' => '<string>',
                'startPos' => '<string>',
            ],
        ];
    }

    public function validate(array $argv)
    {
        if (in_array($argv[1], $this->params['variant'])) {
            if (in_array($argv[2], $this->params['mode'])) {
                switch ($argv[2]) {
                    case GmMode::NAME:
                        return count($argv) - 1 === 3 && in_array($argv[3], $this->params['settings']['color']);
                    case FenMode::NAME:
                        if ($argv[1] === Game::VARIANT_960) {
                            return count($argv) - 1 === 4 ||
                                count($argv) - 1 === 2;
                        } else {
                            return count($argv) - 1 === 3 ||
                                count($argv) - 1 === 2;
                        }
                    case SanMode::NAME:
                        return count($argv) - 1 === 3;
                    case RavMode::NAME:
                        return count($argv) - 1 === 3;
                    case PlayMode::NAME:
                        return count($argv) - 1 === 3;
                    case StockfishMode::NAME:
                        return count($argv) - 1 === 3;
                    default:
                        // do nothing
                        break;
                }
            }
        }

        return false;
    }

    public function run(Socket $socket, array $argv, ConnectionInterface $from)
    {
        if (FenMode::NAME === $argv[2]) {
            try {
                if ($argv[1] === Game::VARIANT_960) {
                    if (isset($argv[3]) && isset($argv[4])) {
                        $startPos = str_split($argv[4]);
                        $board = (new Chess960FenStrToBoard($argv[3], $startPos))
                            ->create();
                    } else {
                        $startPos = (new StartPosition())->create();
                        $board = new Chess960Board($startPos);
                    }
                } elseif ($argv[1] === Game::VARIANT_CAPABLANCA) {
                    if (isset($argv[3])) {
                        $board = (new CapablancaFenStrToBoard($argv[3]))->create();
                    } else {
                        $board =  new CapablancaBoard();
                    }
                } else {
                    if (isset($argv[3])) {
                        $board = (new ClassicalFenStrToBoard($argv[3]))->create();
                    } else {
                        $board =  new ClassicalBoard();
                    }
                }
                $fenMode = new FenMode(
                    new Game($argv[1], $argv[2]),
                    [$from->resourceId],
                    $board->toFen()
                );
                $fenMode->getGame()->setBoard($board);
                $socket->getGameModeStorage()->set($fenMode);
                return $socket->sendToOne($from->resourceId, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'fen' => $board->toFen(),
                        ...($argv[1] === Game::VARIANT_960
                            ? ['startPos' => implode('', $startPos)]
                            : []
                        ),
                    ],
                ]);
            } catch (\Throwable $e) {
                return $socket->sendToOne($from->resourceId, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'message' => 'This FEN string could not be loaded.',
                    ],
                ]);
            }
        } elseif (GmMode::NAME === $argv[2]) {
            $gmMode = new GmMode(
                new Game($argv[1], $argv[2], $socket->getGm()),
                [$from->resourceId]
            );
            $socket->getGameModeStorage()->set($gmMode);
            return $socket->sendToOne($from->resourceId, [
                $this->name => [
                    'variant' => $argv[1],
                    'mode' => $argv[2],
                    'color' => $argv[3],
                ],
            ]);
        } elseif (SanMode::NAME === $argv[2]) {
            try {
                $settings = (object) json_decode(stripslashes($argv[3]), true);
                if ($argv[1] === Game::VARIANT_960) {
                    $startPos = str_split($settings->startPos);
                    $board = new Chess960Board($startPos);
                    $sanPlay = (new SanPlay($settings->movetext, $board));
                } elseif ($argv[1] === Game::VARIANT_CAPABLANCA) {
                    $board = new CapablancaBoard();
                    $sanPlay = (new SanPlay($settings->movetext, $board));
                } else {
                    $sanPlay = new SanPlay($settings->movetext);
                }
                $board = $sanPlay->validate()->getBoard();
                $sanMode = new SanMode(new Game($argv[1], $argv[2]), [$from->resourceId]);
                $game = $sanMode->getGame()->setBoard($board);
                $sanMode->setGame($game);
                $socket->getGameModeStorage()->set($sanMode);
                return $socket->sendToOne($from->resourceId, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'turn' => $game->state()->turn,
                        'filtered' => $sanPlay->getSanMovetext()->filtered(),
                        'movetext' => $sanPlay->getSanMovetext()->validate(),
                        'fen' => $sanPlay->getFen(),
                        ...($argv[1] === Game::VARIANT_960
                            ? ['startPos' =>  $settings->startPos
                            : []
                        ),
                    ],
                ]);
            } catch (\Throwable $e) {
                return $socket->sendToOne($from->resourceId, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'message' => 'This PGN movetext could not be loaded.',
                    ],
                ]);
            }
        } elseif (RavMode::NAME === $argv[2]) {
            try {
                $settings = (object) json_decode(stripslashes($argv[3]), true);
                if ($argv[1] === Game::VARIANT_960) {
                    $startPos = str_split($settings->startPos);
                    $board = new Chess960Board($startPos);
                    $ravPlay = new RavPlay($settings->movetext, $board);
                } elseif ($argv[1] === Game::VARIANT_CAPABLANCA) {
                    $board = new CapablancaBoard();
                    $ravPlay = new RavPlay($settings->movetext, $board);
                } else {
                    $ravPlay = new RavPlay($settings->movetext);
                }
                $board = $ravPlay->validate()->getBoard();
                $sanMode = new SanMode(new Game($argv[1], $argv[2]), [$from->resourceId]);
                $game = $sanMode->getGame()->setBoard($board);
                $sanMode->setGame($game);
                $socket->getGameModeStorage()->set($sanMode);
                return $socket->sendToOne($from->resourceId, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'turn' => $game->state()->turn,
                        'filtered' => $ravPlay->getRavMovetext()->filtered(),
                        'movetext' => $ravPlay->getRavMovetext()->main(),
                        'breakdown' => $ravPlay->getBreakdown(),
                        'fen' => $ravPlay->fen()->getFen(),
                        ...($argv[1] === Game::VARIANT_960
                            ? ['startPos' =>  $settings->startPos
                            : []
                        ),
                    ],
                ]);
            } catch (\Throwable $e) {
                return $socket->sendToOne($from->resourceId, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'message' => 'This PGN movetext could not be loaded.',
                    ],
                ]);
            }
        } elseif (PlayMode::NAME === $argv[2]) {
            $settings = (object) json_decode(stripslashes($argv[3]), true);
            if (isset($settings->fen)) {
                try {
                    if ($argv[1] === Game::VARIANT_960) {
                        $startPos = str_split($settings->startPos);
                        $board = (new Chess960FenStrToBoard($settings->fen, $startPos))
                            ->create();
                    } elseif ($argv[1] === Game::VARIANT_CAPABLANCA) {
                        $board = (new CapablancaFenStrToBoard($settings->fen))
                            ->create();
                    } else {
                        $board = (new ClassicalFenStrToBoard($settings->fen))
                            ->create();
                    }
                } catch (\Throwable $e) {
                    return $socket->sendToOne($from->resourceId, [
                        $this->name => [
                            'variant' => $argv[1],
                            'mode' => $argv[2],
                            'message' => 'This FEN string could not be loaded.',
                        ],
                    ]);
                }
            } else {
                if ($argv[1] === Game::VARIANT_960) {
                    $startPos = (new StartPosition())->create();
                    $board = new Chess960Board($startPos);
                } elseif ($argv[1] === Game::VARIANT_CAPABLANCA) {
                    $board = new CapablancaBoard();
                } else {
                    $board = new ClassicalBoard();
                }
            }
            $game = (new Game($argv[1], $argv[2]))->setBoard($board);
            $payload = [
                'iss' => $_ENV['JWT_ISS'],
                'iat' => time(),
                'exp' => time() + 3600, // one hour by default
                'variant' => $argv[1],
                'submode' => $settings->submode,
                'color' => $settings->color,
                'min' => $settings->min,
                'increment' => $settings->increment,
                'fen' => $game->getBoard()->toFen(),
                ...($argv[1] === Game::VARIANT_960
                    ? ['startPos' => implode('', $game->getBoard()->getStartPos())]
                    : []
                ),
                ...(isset($settings->fen)
                    ? ['fen' => $settings->fen]
                    : []
                ),
            ];
            $jwt = JWT::encode($payload, $_ENV['JWT_SECRET']);
            $playMode = new PlayMode($game, [$from->resourceId], $jwt);
            $socket->getGameModeStorage()->set($playMode);
            if ($settings->submode === PlayMode::SUBMODE_ONLINE) {
                $socket->sendToAll();
            }
            return $socket->sendToOne($from->resourceId, [
                $this->name => [
                    'variant' => $argv[1],
                    'mode' => $argv[2],
                    'fen' => $game->getBoard()->toFen(),
                    'jwt' => $jwt,
                    'hash' => md5($jwt),
                    ...($argv[1] === Game::VARIANT_960
                        ? ['startPos' =>  implode('', $game->getBoard()->getStartPos())]
                        : []
                    ),
                ],
            ]);
        } elseif (StockfishMode::NAME === $argv[2]) {
            if ($argv[3] === Color::W || $argv[3] === Color::B) {
                $stockfishMode = new StockfishMode(
                    new Game($argv[1], $argv[2], $socket->getGm()),
                    [$from->resourceId]
                );
                $socket->getGameModeStorage()->set($stockfishMode);
                return $socket->sendToOne($from->resourceId, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'color' => $argv[3],
                    ],
                ]);
            } else {
                $board = (new ClassicalFenStrToBoard($argv[3]))->create();
                $game = (new Game($argv[1], $argv[2]))->setBoard($board);
                $stockfishMode = new StockfishMode(
                    $game,
                    [$from->resourceId],
                );
                $socket->getGameModeStorage()->set($stockfishMode);
                return $socket->sendToOne($from->resourceId, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'color' => $game->getBoard()->getTurn(),
                        'fen' => $game->getBoard()->toFen(),
                    ],
                ]);
            }
        }
    }
}
