<?php

namespace ChessServer\Command\Game;

use Chess\FenToBoardFactory;
use Chess\Play\SanPlay;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\StartPosition as Chess960StartPosition;
use Chess\Variant\Chess960\FEN\StrToBoard as Chess960FenStrToBoard;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Classical\PGN\AN\Color;
use Chess\Variant\Dunsany\Board as DunsanyBoard;
use Chess\Variant\Losing\Board as LosingBoard;
use Chess\Variant\RacingKings\Board as RacingKingsBoard;
use ChessServer\Command\AbstractCommand;
use ChessServer\Command\Game\Mode\AnalysisMode;
use ChessServer\Command\Game\Mode\PlayMode;
use ChessServer\Command\Game\Mode\StockfishMode;
use ChessServer\Socket\AbstractSocket;
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
                Game::VARIANT_DUNSANY,
                Game::VARIANT_LOSING,
                Game::VARIANT_RACING_KINGS,
            ],
            // mandatory param
            'mode' => [
                AnalysisMode::NAME,
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
                'startPos' => '<string>',
            ],
        ];
    }

    public function validate(array $argv)
    {
        if (in_array($argv[1], $this->params['variant'])) {
            if (in_array($argv[2], $this->params['mode'])) {
                switch ($argv[2]) {
                    case AnalysisMode::NAME:
                        return count($argv) - 1 === 3 || count($argv) - 1 === 2;
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

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        if (AnalysisMode::NAME === $argv[2]) {
            try {
                $settings = isset($argv[3])
                    ? (object) json_decode(stripslashes($argv[3]), true)
                    : (object) [];
                if ($argv[1] === Game::VARIANT_960) {
                    if (isset($settings->startPos) && isset($settings->fen)) {
                        $startPos = str_split($settings->startPos);
                        $board = FenToBoardFactory::create($settings->fen, new Chess960Board($startPos));
                    } else {
                        $startPos = (new Chess960StartPosition())->create();
                        $board = new Chess960Board($startPos);
                    }
                } elseif ($argv[1] === Game::VARIANT_DUNSANY) {
                    $board = isset($settings->fen)
                        ? FenToBoardFactory::create($settings->fen, new DunsanyBoard())
                        : new DunsanyBoard();
                } elseif ($argv[1] === Game::VARIANT_LOSING) {
                    $board = isset($settings->fen)
                        ? FenToBoardFactory::create($settings->fen, new LosingBoard())
                        : new LosingBoard();
                } elseif ($argv[1] === Game::VARIANT_RACING_KINGS) {
                    $board = isset($settings->fen)
                        ? FenToBoardFactory::create($settings->fen, new RacingKingsBoard())
                        : new RacingKingsBoard();
                } else {
                    $board = isset($settings->fen)
                        ? FenToBoardFactory::create($settings->fen, new ClassicalBoard())
                        : new ClassicalBoard();
                }
                $sanPlay = new SanPlay($settings->movetext ?? '', $board);
                $sanPlay->validate();
                $mode = new AnalysisMode(new Game($argv[1], $argv[2]), [$id]);
                $game = $mode->getGame()->setBoard($sanPlay->board);
                $mode->setGame($game);
                $socket->getGameModeStorage()->set($mode);
                return $socket->getClientStorage()->sendToOne($id, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'turn' => $game->state()->turn,
                        'movetext' => $sanPlay->sanMovetext->validate(),
                        'fen' => $sanPlay->fen,
                        ...($argv[1] === Game::VARIANT_960
                            ? ['startPos' =>  implode('', $startPos)]
                            : []
                        ),
                    ],
                ]);
            } catch (\Throwable $e) {
                return $socket->getClientStorage()->sendToOne($id, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'message' => 'This game could not be created.',
                    ],
                ]);
            }
        } elseif (PlayMode::NAME === $argv[2]) {
            $settings = (object) json_decode(stripslashes($argv[3]), true);
            try {
                if ($argv[1] === Game::VARIANT_960) {
                    if (isset($settings->startPos) && isset($settings->fen)) {
                        $startPos = str_split($settings->startPos);
                        $board = FenToBoardFactory::create($settings->fen, new Chess960Board($startPos));
                    } else {
                        $startPos = (new Chess960StartPosition())->create();
                        $board = new Chess960Board($startPos);
                    }
                } elseif ($argv[1] === Game::VARIANT_DUNSANY) {
                    $board = isset($settings->fen)
                        ? FenToBoardFactory::create($settings->fen, new DunsanyBoard())
                        : new DunsanyBoard();
                } elseif ($argv[1] === Game::VARIANT_LOSING) {
                    $board = isset($settings->fen)
                        ? FenToBoardFactory::create($settings->fen, new LosingBoard())
                        : new LosingBoard();
                } elseif ($argv[1] === Game::VARIANT_RACING_KINGS) {
                    $board = isset($settings->fen)
                        ? FenToBoardFactory::create($settings->fen, new RacingKingsBoard())
                        : new RacingKingsBoard();
                } else {
                    $board = isset($settings->fen)
                        ? FenToBoardFactory::create($settings->fen, new ClassicalBoard())
                        : new ClassicalBoard();
                }
                $game = (new Game($argv[1], $argv[2]))->setBoard($board);
                $payload = [
                    'iss' => $_ENV['JWT_ISS'],
                    'iat' => time(),
                    'exp' => time() + 3600, // one hour by default
                    'variant' => $argv[1],
                    'username' => [
                        Color::W => $settings->color === Color::W && $settings->username ? $settings->username : 'anonymous',
                        Color::B => $settings->color === Color::B && $settings->username ? $settings->username : 'anonymous',
                    ],
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
                $mode = new PlayMode($game, [$id], $jwt);
                $socket->getGameModeStorage()->set($mode);
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
            } catch (\Throwable $e) {
                return $socket->getClientStorage()->sendToOne($id, [
                    $this->name => [
                        'variant' => $argv[1],
                        'mode' => $argv[2],
                        'message' => 'This game could not be created.',
                    ],
                ]);
            }
        } elseif (StockfishMode::NAME === $argv[2]) {
            $settings = (object) json_decode(stripslashes($argv[3]), true);
            if (isset($settings->fen)) {
                $board = FenToBoardFactory::create($settings->fen, new ClassicalBoard());
                $game = (new Game($argv[1], $argv[2]))->setBoard($board);
            } else {
                $game = new Game($argv[1], $argv[2], $socket->getGmMove());
            }
            $mode = new StockfishMode($game, [$id]);
            $socket->getGameModeStorage()->set($mode);
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
