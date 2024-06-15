<?php

namespace ChessServer\Command;

use Chess\FenToBoardFactory;
use Chess\Play\SanPlay;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\StartPosition as Chess960StartPosition;
use Chess\Variant\Chess960\FEN\StrToBoard as Chess960FenStrToBoard;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Classical\FEN\StrToBoard as ClassicalFenStrToBoard;
use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Game\Game;
use ChessServer\Game\FenMode;
use ChessServer\Game\PlayMode;
use ChessServer\Game\SanMode;
use ChessServer\Game\StockfishMode;
use ChessServer\Socket\ChesslaBlabSocket;
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

    public function run(ChesslaBlabSocket $socket, array $argv, int $id)
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
                        $startPos = (new Chess960StartPosition())->create();
                        $board = new Chess960Board($startPos);
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
                    [$id],
                    $board->toFen()
                );
                $fenMode->getGame()->setBoard($board);
                $socket->getGameModeStorage()->set($fenMode);
                return $socket->getClientStorage()->sendToOne($id, [
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
                return $socket->getClientStorage()->sendToOne($id, [
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
                        $board = FenToBoardFactory::create($settings->fen, $board);
                    }
                    $sanPlay = new SanPlay($settings->movetext, $board);
                } else {
                    $board = new ClassicalBoard();
                    if (isset($settings->fen)) {
                        $board = FenToBoardFactory::create($settings->fen, $board);
                    }
                    $sanPlay = new SanPlay($settings->movetext, $board);
                }
                $sanPlay->validate();
                $sanMode = new SanMode(new Game($argv[1], $argv[2]), [$id]);
                $game = $sanMode->getGame()->setBoard($sanPlay->board);
                $sanMode->setGame($game);
                $socket->getGameModeStorage()->set($sanMode);
                return $socket->getClientStorage()->sendToOne($id, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'turn' => $game->state()->turn,
                        'movetext' => $sanPlay->sanMovetext->validate(),
                        'fen' => $sanPlay->fen,
                        ...($argv[1] === Game::VARIANT_960
                            ? ['startPos' =>  $settings->startPos]
                            : []
                        ),
                    ],
                ]);
            } catch (\Throwable $e) {
                return $socket->getClientStorage()->sendToOne($id, [
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
                    } else {
                        $board = (new ClassicalFenStrToBoard($settings->fen))
                            ->create();
                    }
                } catch (\Throwable $e) {
                    return $socket->getClientStorage()->sendToOne($id, [
                        $this->name => [
                            'variant' => $argv[1],
                            'mode' => $argv[2],
                            'message' => 'This FEN string could not be loaded.',
                        ],
                    ]);
                }
            } else {
                if ($argv[1] === Game::VARIANT_960) {
                    $startPos = (new Chess960StartPosition())->create();
                    $board = new Chess960Board($startPos);
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
            $playMode = new PlayMode($game, [$id], $jwt);
            $socket->getGameModeStorage()->set($playMode);
            if ($settings->submode === PlayMode::SUBMODE_ONLINE) {
                $socket->getClientStorage()->sendToAll([
                    'broadcast' => [
                        'onlineGames' => $socket->getGameModeStorage()
                            ->decodeByPlayMode(PlayMode::STATUS_PENDING, PlayMode::SUBMODE_ONLINE),
                    ],
                ]);
            }
            return $socket->getClientStorage()->sendToOne($id, [
                $this->name => [
                    'variant' => $argv[1],
                    'mode' => $argv[2],
                    'fen' => $game->getBoard()->toFen(),
                    'jwt' => $jwt,
                    'hash' => hash('adler32', $jwt),
                    ...($argv[1] === Game::VARIANT_960
                        ? ['startPos' =>  implode('', $game->getBoard()->getStartPos())]
                        : []
                    ),
                ],
            ]);
        } elseif (StockfishMode::NAME === $argv[2]) {
            $settings = (object) json_decode(stripslashes($argv[3]), true);
            if (isset($settings->fen)) {
                $board = (new ClassicalFenStrToBoard($settings->fen))->create();
                $game = (new Game($argv[1], $argv[2]))->setBoard($board);
            } else {
                $game = new Game($argv[1], $argv[2], $socket->getGmMove());
            }
            $stockfishMode = new StockfishMode($game, [$id]);
            $socket->getGameModeStorage()->set($stockfishMode);
            return $socket->getClientStorage()->sendToOne($id, [
                $this->name => [
                    'variant' => $argv[1],
                    'mode' => $argv[2],
                    'color' => $settings->color,
                    'fen' => $game->getBoard()->toFen(),
                ],
            ]);
        }
    }
}
