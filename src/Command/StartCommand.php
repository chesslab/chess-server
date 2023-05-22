<?php

namespace ChessServer\Command;

use Chess\Game;
use Chess\Movetext;
use Chess\Player\PgnPlayer;
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
use ChessServer\GameMode\GmMode;
use ChessServer\GameMode\FenMode;
use ChessServer\GameMode\PgnMode;
use ChessServer\GameMode\PlayMode;
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
                Game::VARIANT_CAPABLANCA_80,
                Game::VARIANT_CAPABLANCA_100,
                Game::VARIANT_CLASSICAL,
            ],
            // mandatory param
            'mode' => [
                GmMode::NAME,
                FenMode::NAME,
                PgnMode::NAME,
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
                            return count($argv) - 1 === 4;
                        } else {
                            return count($argv) - 1 === 3;
                        }
                    case PgnMode::NAME:
                        if ($argv[1] === Game::VARIANT_960) {
                            return count($argv) - 1 === 4;
                        } else {
                            return count($argv) - 1 === 3;
                        }
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
                    $startPos = str_split($argv[4]);
                    $board = (new Chess960FenStrToBoard($argv[3], $startPos))
                        ->create();
                } elseif ($argv[1] === Game::VARIANT_CAPABLANCA_80) {
                    $board = (new Capablanca80FenStrToBoard($argv[3]))
                        ->create();
                } else {
                    $board = (new ClassicalFenStrToBoard($argv[3]))
                        ->create();
                }
                $fenMode = new FenMode(
                    new Game($argv[1], $argv[2]),
                    [$from->resourceId],
                    $argv[3]
                );
                $fenMode->getGame()->setBoard($board);
                $socket->getGameModeStorage()->set($fenMode);
                return $socket->sendToOne($from->resourceId, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'fen' => $argv[3],
                        ...($argv[1] === Game::VARIANT_960
                            ? ['startPos' =>  $argv[4]]
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
        } elseif (PgnMode::NAME === $argv[2]) {
            try {
                if ($argv[1] === Game::VARIANT_960) {
                    $move = new ClassicalPgnMove();
                    $movetext = (new Movetext($move, $argv[3]))->validate();
                    $startPos = str_split($argv[4]);
                    $board = new Chess960Board($startPos);
                    $player = (new PgnPlayer($movetext, $board))->play();
                } elseif ($argv[1] === Game::VARIANT_CAPABLANCA_80) {
                    $move = new Capablanca80PgnMove();
                    $movetext = (new Movetext($move, $argv[3]))->validate();
                    $board = new Capablanca80Board();
                    $player = (new PgnPlayer($movetext, $board))->play();
                } else {
                    $move = new ClassicalPgnMove();
                    $movetext = (new Movetext($move, $argv[3]))->validate();
                    $player = (new PgnPlayer($movetext))->play();
                }
                $pgnMode = new PgnMode(new Game($argv[1], $argv[2]), [$from->resourceId]);
                $game = $pgnMode->getGame()->setBoard($player->getBoard());
                $pgnMode->setGame($game);
                $socket->getGameModeStorage()->set($pgnMode);
                return $socket->sendToOne($from->resourceId, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'turn' => $game->state()->turn,
                        'movetext' => $movetext,
                        'fen' => $player->getFen(),
                        ...($argv[1] === Game::VARIANT_960
                            ? ['startPos' =>  $argv[4]]
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
                    } elseif ($argv[1] === Game::VARIANT_CAPABLANCA_80) {
                        $board = (new Capablanca80FenStrToBoard($settings->fen))
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
                } elseif ($argv[1] === Game::VARIANT_CAPABLANCA_80) {
                    $board = new Capablanca80Board();
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
            try {
                $stockfishMode = new StockfishMode(
                    new Game($argv[1], $argv[2]),
                    [$from->resourceId],
                    $argv[3]
                );
                $game = $stockfishMode->getGame();
                $game->loadFen($argv[3]);
                $stockfishMode->setGame($game);
                $socket->getGameModeStorage()->set($stockfishMode);
                return $socket->sendToOne($from->resourceId, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'color' => $game->getBoard()->getTurn(),
                        'fen' => $game->getBoard()->toFen(),
                    ],
                ]);
            } catch (\Throwable $e) {
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
                    return $socket->sendToOne($from->resourceId, [
                        $this->name => [
                            'variant' => $argv[1],
                            'mode' => $argv[2],
                            'message' => 'Stockfish could not be started.',
                        ],
                    ]);
                }
            }
        }
    }
}
