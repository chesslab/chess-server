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
use ChessServer\Db;
use ChessServer\Command\AbstractCommand;
use ChessServer\Command\Game\Mode\AnalysisMode;
use ChessServer\Command\Game\Mode\PlayMode;
use ChessServer\Command\Game\Mode\StockfishMode;
use ChessServer\Socket\AbstractSocket;
use Firebase\JWT\JWT;

class StartCommand extends AbstractCommand
{
    public function __construct(Db $db)
    {
        parent::__construct($db);

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
                        'variant' => $params['variant'],
                        'mode' => $params['mode'],
                        'turn' => $game->state()->turn,
                        'movetext' => $sanPlay->sanMovetext->validate(),
                        'fen' => $sanPlay->fen,
                        ...($params['variant'] === Game::VARIANT_960
                            ? ['startPos' =>  implode('', $startPos)]
                            : []
                        ),
                    ],
                ]);
            } catch (\Throwable $e) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => [
                        'variant' => $params['variant'],
                        'mode' => $params['mode'],
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
                        Color::W => $params['settings']['color'] === Color::W && $params['settings']['elo']
                            ? $params['settings']['elo']
                            : null,
                        Color::B => $params['settings']['color'] === Color::B && $params['settings']['elo']
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
                    ...(isset($params['settings']['fen'])
                        ? ['fen' => $params['settings']['fen']]
                        : []
                    ),
                ];
                $gameMode = new PlayMode(
                    $game,
                    [$id],
                    JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256'),
                    $this->db
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
                        'variant' => $params['variant'],
                        'mode' => $params['mode'],
                        'fen' => $game->getBoard()->toFen(),
                        'jwt' => $gameMode->getJwt(),
                        'hash' => $gameMode->getHash(),
                        ...($params['variant'] === Game::VARIANT_960
                            ? ['startPos' =>  implode('', $game->getBoard()->getStartPos())]
                            : []
                        ),
                    ],
                ]);
            } catch (\Throwable $e) {
                return $socket->getClientStorage()->send([$id], [
                    $this->name => [
                        'variant' => $params['variant'],
                        'mode' => $params['mode'],
                        'message' => 'This game could not be created.',
                    ],
                ]);
            }
        } elseif (StockfishMode::NAME === $params['mode']) {
            if (isset($params['settings']['fen'])) {
                $board = FenToBoardFactory::create($params['settings']['fen'], new ClassicalBoard());
                $game = (new Game($params['variant'], $params['mode']))->setBoard($board);
            } else {
                $game = new Game($params['variant'], $params['mode'], $socket->getGmMove());
            }
            $gameMode = new StockfishMode($game, [$id]);
            $socket->getGameModeStorage()->set($gameMode);
            return $socket->getClientStorage()->send([$id], [
                $this->name => [
                    'variant' => $params['variant'],
                    'mode' => $params['mode'],
                    'color' => $params['settings']['color'],
                    'fen' => $game->getBoard()->toFen(),
                ],
            ]);
        }
    }
}
