<?php

namespace ChessServer\Command;

use Chess\FenToBoard;
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
use ChessServer\Socket\ChessSocket;
use ChessServer\GameMode\FenMode;
use ChessServer\GameMode\PlayMode;
use ChessServer\GameMode\SanMode;
use ChessServer\GameMode\StockfishMode;
use Firebase\JWT\JWT;

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
                FenMode::NAME,
                SanMode::NAME,
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
                    case FenMode::NAME:
                        return count($argv) - 1 === 3 ||
                            count($argv) - 1 === 2;
                    case SanMode::NAME:
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

    public function run(ChessSocket $socket, array $argv, int $resourceId)
    {
        if (FenMode::NAME === $argv[2]) {
            try {
                if (isset($argv[3])) {
                    $settings = (object) json_decode(stripslashes($argv[3]), true);
                }
                if ($argv[1] === Game::VARIANT_960) {
                    if (isset($settings->fen) && isset($settings->startPos)) {
                        $startPos = str_split($settings->startPos);
                        $board = (new Chess960FenStrToBoard($settings->fen, $startPos))
                            ->create();
                    } else {
                        $startPos = (new StartPosition())->create();
                        $board = new Chess960Board($startPos);
                    }
                } elseif ($argv[1] === Game::VARIANT_CAPABLANCA) {
                    if (isset($settings->fen)) {
                        $board = (new CapablancaFenStrToBoard($settings->fen))->create();
                    } else {
                        $board =  new CapablancaBoard();
                    }
                } else {
                    if (isset($settings->fen)) {
                        $board = (new ClassicalFenStrToBoard($settings->fen))->create();
                    } else {
                        $board =  new ClassicalBoard();
                    }
                }
                $fenMode = new FenMode(
                    new Game($argv[1], $argv[2]),
                    [$resourceId],
                    $board->toFen()
                );
                $fenMode->getGame()->setBoard($board);
                $socket->getGameModeStorage()->set($fenMode);
                return $socket->sendToOne($resourceId, [
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
                return $socket->sendToOne($resourceId, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'message' => 'This FEN string could not be loaded.',
                    ],
                ]);
            }
        } elseif (SanMode::NAME === $argv[2]) {
            try {
                $settings = (object) json_decode(stripslashes($argv[3]), true);
                if ($argv[1] === Game::VARIANT_960) {
                    $startPos = str_split($settings->startPos);
                    $board = new Chess960Board($startPos);
                    if (isset($settings->fen)) {
                        $board = FenToBoard::create($settings->fen, $board);
                    }
                    $sanPlay = new SanPlay($settings->movetext, $board);
                } elseif ($argv[1] === Game::VARIANT_CAPABLANCA) {
                    $board = new CapablancaBoard();
                    if (isset($settings->fen)) {
                        $board = FenToBoard::create($settings->fen, $board);
                    }
                    $sanPlay = new SanPlay($settings->movetext, $board);
                } else {
                    $board = new ClassicalBoard();
                    if (isset($settings->fen)) {
                        $board = FenToBoard::create($settings->fen, $board);
                    }
                    $sanPlay = new SanPlay($settings->movetext, $board);
                }
                $sanPlay->validate();
                $board = $sanPlay->getBoard();
                $sanMode = new SanMode(new Game($argv[1], $argv[2]), [$resourceId]);
                $game = $sanMode->getGame()->setBoard($board);
                $sanMode->setGame($game);
                $socket->getGameModeStorage()->set($sanMode);
                return $socket->sendToOne($resourceId, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'turn' => $game->state()->turn,
                        'movetext' => $sanPlay->getSanMovetext()->validate(),
                        'fen' => $sanPlay->getFen(),
                        ...($argv[1] === Game::VARIANT_960
                            ? ['startPos' =>  $settings->startPos]
                            : []
                        ),
                    ],
                ]);
            } catch (\Throwable $e) {
                return $socket->sendToOne($resourceId, [
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
                    return $socket->sendToOne($resourceId, [
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
            $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
            $playMode = new PlayMode($game, [$resourceId], $jwt);
            $socket->getGameModeStorage()->set($playMode);
            if ($settings->submode === PlayMode::SUBMODE_ONLINE) {
                $socket->sendToAll();
            }
            return $socket->sendToOne($resourceId, [
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
                    [$resourceId]
                );
                $socket->getGameModeStorage()->set($stockfishMode);
                return $socket->sendToOne($resourceId, [
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
                    [$resourceId],
                );
                $socket->getGameModeStorage()->set($stockfishMode);
                return $socket->sendToOne($resourceId, [
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
