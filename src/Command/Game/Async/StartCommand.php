<?php

namespace ChessServer\Command\Game\Sync;

use Chess\FenToBoardFactory;
use Chess\Play\SanPlay;
use Chess\Variant\Capablanca\Board as CapablancaBoard;
use Chess\Variant\CapablancaFischer\Board as CapablancaFischerBoard;
use Chess\Variant\CapablancaFischer\StartPosition as CapablancaFischerStartPosition;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\StartPosition as Chess960StartPosition;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Classical\PGN\AN\Color;
use Chess\Variant\Dunsany\Board as DunsanyBoard;
use Chess\Variant\Losing\Board as LosingBoard;
use Chess\Variant\RacingKings\Board as RacingKingsBoard;
use ChessServer\Command\AbstractSyncCommand;
use ChessServer\Command\Game\Game;
use ChessServer\Command\Game\Mode\AnalysisMode;
use ChessServer\Command\Game\Mode\PlayMode;
use ChessServer\Command\Game\Mode\StockfishMode;
use ChessServer\Socket\AbstractSocket;
use Firebase\JWT\JWT;

class StartCommand extends AbstractSyncCommand
{
    public function __construct()
    {
        $this->name = '/start';
        $this->description = 'Starts a new game.';
        $this->params = [
            'params' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $params = json_decode(stripslashes($argv[1]), true);

        if (AnalysisMode::NAME === $params['mode']) {
            try {
                if ($params['variant'] === Game::VARIANT_960) {
                    if (isset($params['settings']['startPos']) && isset($params['settings']['fen'])) {
                        $startPos = str_split($params['settings']['startPos']);
                        $board = FenToBoardFactory::create($params['settings']['fen'], new Chess960Board($startPos));
                    } else {
                        $startPos = (new Chess960StartPosition())->create();
                        $board = new Chess960Board($startPos);
                    }
                } elseif ($params['variant'] === Game::VARIANT_CAPABLANCA) {
                    $board = isset($params['settings']['fen'])
                        ? FenToBoardFactory::create($params['settings']['fen'], new CapablancaBoard())
                        : new CapablancaBoard();
                } elseif ($params['variant'] === Game::VARIANT_CAPABLANCA_FISCHER) {
                    if (isset($params['settings']['startPos']) && isset($params['settings']['fen'])) {
                        $startPos = str_split($params['settings']['startPos']);
                        $board = FenToBoardFactory::create($params['settings']['fen'], new CapablancaFischerBoard($startPos));
                    } else {
                        $startPos = (new CapablancaFischerStartPosition())->create();
                        $board = new CapablancaFischerBoard($startPos);
                    }
                } elseif ($params['variant'] === Game::VARIANT_DUNSANY) {
                    $board = isset($params['settings']['fen'])
                        ? FenToBoardFactory::create($params['settings']['fen'], new DunsanyBoard())
                        : new DunsanyBoard();
                } elseif ($params['variant'] === Game::VARIANT_LOSING) {
                    $board = isset($params['settings']['fen'])
                        ? FenToBoardFactory::create($params['settings']['fen'], new LosingBoard())
                        : new LosingBoard();
                } elseif ($params['variant'] === Game::VARIANT_RACING_KINGS) {
                    $board = isset($params['settings']['fen'])
                        ? FenToBoardFactory::create($params['settings']['fen'], new RacingKingsBoard())
                        : new RacingKingsBoard();
                } else {
                    $board = isset($params['settings']['fen'])
                        ? FenToBoardFactory::create($params['settings']['fen'], new ClassicalBoard())
                        : new ClassicalBoard();
                }
                $sanPlay = new SanPlay($params['settings']['movetext'] ?? '', $board);
                $sanPlay->validate();
                $gameMode = new AnalysisMode(new Game($params['variant'], $params['mode']), [$id]);
                $game = $gameMode->getGame()->setBoard($sanPlay->board);
                $gameMode->setGame($game);
                $socket->getGameModeStorage()->set($gameMode);
                return $socket->getClientStorage()->send([$id], [
                    $this->name => [
                        'variant' => $game->getVariant(),
                        'mode' => $game->getMode(),
                        'turn' => $game->state()->turn,
                        'movetext' => $sanPlay->sanMovetext->validate(),
                        'fen' => $sanPlay->fen,
                        ...($params['variant'] === Game::VARIANT_960
                            ? ['startPos' =>  implode('', $startPos)]
                            : []
                        ),
                        ...($params['variant'] === Game::VARIANT_CAPABLANCA_FISCHER
                            ? ['startPos' =>  implode('', $startPos)]
                            : []
                        ),
                    ],
                ]);
            } catch (\Throwable $e) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => [
                        'message' => 'This game could not be created.',
                    ],
                ]);
            }
        } elseif (PlayMode::NAME === $params['mode']) {
            try {
                if ($params['variant'] === Game::VARIANT_960) {
                    if (isset($params['settings']['startPos']) && isset($params['settings']['fen'])) {
                        $startPos = str_split($params['settings']['startPos']);
                        $board = FenToBoardFactory::create($params['settings']['fen'], new Chess960Board($startPos));
                    } else {
                        $startPos = (new Chess960StartPosition())->create();
                        $board = new Chess960Board($startPos);
                    }
                } elseif ($params['variant'] === Game::VARIANT_CAPABLANCA) {
                    $board = isset($params['settings']['fen'])
                        ? FenToBoardFactory::create($params['settings']['fen'], new CapablancaBoard())
                        : new CapablancaBoard();
                } elseif ($params['variant'] === Game::VARIANT_CAPABLANCA_FISCHER) {
                    if (isset($params['settings']['startPos']) && isset($params['settings']['fen'])) {
                        $startPos = str_split($params['settings']['startPos']);
                        $board = FenToBoardFactory::create($params['settings']['fen'], new CapablancaFischerBoard($startPos));
                    } else {
                        $startPos = (new CapablancaFischerStartPosition())->create();
                        $board = new CapablancaFischerBoard($startPos);
                    }
                } elseif ($params['variant'] === Game::VARIANT_DUNSANY) {
                    $board = isset($params['settings']['fen'])
                        ? FenToBoardFactory::create($params['settings']['fen'], new DunsanyBoard())
                        : new DunsanyBoard();
                } elseif ($params['variant'] === Game::VARIANT_LOSING) {
                    $board = isset($params['settings']['fen'])
                        ? FenToBoardFactory::create($params['settings']['fen'], new LosingBoard())
                        : new LosingBoard();
                } elseif ($params['variant'] === Game::VARIANT_RACING_KINGS) {
                    $board = isset($params['settings']['fen'])
                        ? FenToBoardFactory::create($params['settings']['fen'], new RacingKingsBoard())
                        : new RacingKingsBoard();
                } else {
                    $board = isset($params['settings']['fen'])
                        ? FenToBoardFactory::create($params['settings']['fen'], new ClassicalBoard())
                        : new ClassicalBoard();
                }
                $game = (new Game($params['variant'], $params['mode']))->setBoard($board);
                $payload = [
                    'iss' => $_ENV['JWT_ISS'],
                    'iat' => time(),
                    'exp' => time() + 3600, // one hour by default
                    'variant' => $params['variant'],
                    'username' => [
                        Color::W => $params['settings']['color'] === Color::W && $params['settings']['username']
                            ? $params['settings']['username']
                            : self::ANONYMOUS_USER,
                        Color::B => $params['settings']['color'] === Color::B && $params['settings']['username']
                            ? $params['settings']['username']
                            : self::ANONYMOUS_USER,
                    ],
                    'elo' => [
                        Color::W => $params['settings']['color'] === Color::W && isset($params['settings']['elo'])
                            ? $params['settings']['elo']
                            : null,
                        Color::B => $params['settings']['color'] === Color::B && isset($params['settings']['elo'])
                            ? $params['settings']['elo']
                            : null,
                    ],
                    'submode' => $params['settings']['submode'],
                    'color' => $params['settings']['color'],
                    'min' => $params['settings']['min'],
                    'increment' => $params['settings']['increment'],
                    'fen' => $game->getBoard()->toFen(),
                    ...($params['variant'] === Game::VARIANT_960
                        ? ['startPos' => implode('', $game->getBoard()->getStartPos())]
                        : []
                    ),
                    ...($params['variant'] === Game::VARIANT_CAPABLANCA_FISCHER
                        ? ['startPos' => implode('', $game->getBoard()->getStartPos())]
                        : []
                    ),
                    ...(isset($params['settings']['fen'])
                        ? ['fen' => $params['settings']['fen']]
                        : []
                    ),
                ];
                $payload['uid'] = hash('adler32', json_encode($payload));
                $gameMode = new PlayMode(
                    $game,
                    [$id],
                    JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256')
                );
                $socket->getGameModeStorage()->set($gameMode);
                if ($params['settings']['submode'] === PlayMode::SUBMODE_ONLINE) {
                    $socket->getClientStorage()->broadcast([
                        'broadcast' => [
                            'onlineGames' => $socket->getGameModeStorage()
                                ->decodeByPlayMode(PlayMode::STATUS_PENDING, PlayMode::SUBMODE_ONLINE),
                        ],
                    ]);
                }
                return $socket->getClientStorage()->send([$id], [
                    $this->name => [
                        'uid' => $gameMode->getUid(),
                        'jwt' => $gameMode->getJwt(),
                        'variant' => $game->getVariant(),
                        'mode' => $game->getMode(),
                        'fen' => $game->getBoard()->toFen(),
                        ...($params['variant'] === Game::VARIANT_960
                            ? ['startPos' =>  implode('', $game->getBoard()->getStartPos())]
                            : []
                        ),
                        ...($params['variant'] === Game::VARIANT_CAPABLANCA_FISCHER
                            ? ['startPos' =>  implode('', $game->getBoard()->getStartPos())]
                            : []
                        ),
                    ],
                ]);
            } catch (\Throwable $e) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => [
                        'message' => 'This game could not be created.',
                    ],
                ]);
            }
        } elseif (StockfishMode::NAME === $params['mode']) {
            if (isset($params['settings']['fen'])) {
                $board = FenToBoardFactory::create($params['settings']['fen'], new ClassicalBoard());
                $game = (new Game($params['variant'], $params['mode']))->setBoard($board);
            } else {
                $game = new Game($params['variant'], $params['mode']);
            }
            $payload = [
                'iss' => $_ENV['JWT_ISS'],
                'iat' => time(),
                'exp' => time() + 3600, // one hour by default
                ...$params,
            ];
            $gameMode = new StockfishMode(
                $game,
                [$id],
                JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256')
            );
            $socket->getGameModeStorage()->set($gameMode);
            return $socket->getClientStorage()->send([$id], [
                $this->name => [
                    'uid' => $gameMode->getUid(),
                    'jwt' => $gameMode->getJwt(),
                    'variant' => $game->getVariant(),
                    'mode' => $game->getMode(),
                    'color' => $params['settings']['color'],
                    'fen' => $game->getBoard()->toFen(),
                ],
            ]);
        }
    }
}
