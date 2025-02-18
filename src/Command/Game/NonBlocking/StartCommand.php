<?php

namespace ChessServer\Command\Game\NonBlocking;

use Chess\Play\SanPlay;
use Chess\Variant\VariantType;
use Chess\Variant\Capablanca\Board as CapablancaBoard;
use Chess\Variant\Capablanca\FenToBoardFactory as CapablancaFenToBoardFactory;
use Chess\Variant\CapablancaFischer\Board as CapablancaFischerBoard;
use Chess\Variant\CapablancaFischer\FenToBoardFactory as CapablancaFischerFenToBoardFactory;
use Chess\Variant\Chess960\Board as Chess960Board;
use Chess\Variant\Chess960\FenToBoardFactory as Chess960FenToBoardFactory;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Classical\FenToBoardFactory as ClassicalFenToBoardFactory;
use Chess\Variant\Classical\PGN\Color;
use Chess\Variant\Dunsany\Board as DunsanyBoard;
use Chess\Variant\Dunsany\FenToBoardFactory as DunsanyFenToBoardFactory;
use Chess\Variant\Losing\Board as LosingBoard;
use Chess\Variant\Losing\FenToBoardFactory as LosingFenToBoardFactory;
use Chess\Variant\RacingKings\Board as RacingKingsBoard;
use Chess\Variant\RacingKings\FenToBoardFactory as RacingKingsFenToBoardFactory;
use ChessServer\Command\AbstractNonBlockingCommand;
use ChessServer\Command\Game\Game;
use ChessServer\Command\Game\Mode\AnalysisMode;
use ChessServer\Command\Game\Mode\PlayMode;
use ChessServer\Command\Game\Mode\StockfishMode;
use ChessServer\Socket\AbstractSocket;
use Firebase\JWT\JWT;

class StartCommand extends AbstractNonBlockingCommand
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
        try {
            $params = $this->params($argv[1]);
            if (AnalysisMode::NAME === $params['mode']) {
                if ($params['variant'] === VariantType::CHESS_960) {
                    $board = isset($params['settings']['fen']) 
                        ? Chess960FenToBoardFactory::create($params['settings']['fen'])
                        : new Chess960Board();
                } elseif ($params['variant'] === VariantType::CAPABLANCA) {
                    $board = isset($params['settings']['fen'])
                        ? CapablancaFenToBoardFactory::create($params['settings']['fen'])
                        : new CapablancaBoard();
                } elseif ($params['variant'] === VariantType::CAPABLANCA_FISCHER) {
                    $board = isset($params['settings']['fen'])
                        ? CapablancaFischerFenToBoardFactory::create($params['settings']['fen'])
                        : new CapablancaFischerBoard();
                } elseif ($params['variant'] === VariantType::DUNSANY) {
                    $board = isset($params['settings']['fen'])
                        ? DunsanyFenToBoardFactory::create($params['settings']['fen'])
                        : new DunsanyBoard();
                } elseif ($params['variant'] === VariantType::LOSING) {
                    $board = isset($params['settings']['fen'])
                        ? LosingFenToBoardFactory::create($params['settings']['fen'])
                        : new LosingBoard();
                } elseif ($params['variant'] === VariantType::RACING_KINGS) {
                    $board = isset($params['settings']['fen'])
                        ? RacingKingsFenToBoardFactory::create($params['settings']['fen'])
                        : new RacingKingsBoard();
                } else {
                    $board = isset($params['settings']['fen'])
                        ? ClassicalFenToBoardFactory::create($params['settings']['fen'])
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
                    ],
                ]);
            } elseif (PlayMode::NAME === $params['mode']) {
                if ($params['variant'] === VariantType::CHESS_960) {
                    $board = isset($params['settings']['fen']) 
                        ? Chess960FenToBoardFactory::create($params['settings']['fen'])
                        : new Chess960Board();
                } elseif ($params['variant'] === VariantType::CAPABLANCA) {
                    $board = isset($params['settings']['fen'])
                        ? CapablancaFenToBoardFactory::create($params['settings']['fen'])
                        : new CapablancaBoard();
                } elseif ($params['variant'] === VariantType::CAPABLANCA_FISCHER) {
                    $board = isset($params['settings']['fen'])
                        ? CapablancaFischerFenToBoardFactory::create($params['settings']['fen'])
                        : new CapablancaFischerBoard();
                } elseif ($params['variant'] === VariantType::DUNSANY) {
                    $board = isset($params['settings']['fen'])
                        ? DunsanyFenToBoardFactory::create($params['settings']['fen'])
                        : new DunsanyBoard();
                } elseif ($params['variant'] === VariantType::LOSING) {
                    $board = isset($params['settings']['fen'])
                        ? LosingFenToBoardFactory::create($params['settings']['fen'])
                        : new LosingBoard();
                } elseif ($params['variant'] === VariantType::RACING_KINGS) {
                    $board = isset($params['settings']['fen'])
                        ? RacingKingsFenToBoardFactory::create($params['settings']['fen'])
                        : new RacingKingsBoard();
                } else {
                    $board = isset($params['settings']['fen'])
                        ? ClassicalFenToBoardFactory::create($params['settings']['fen'])
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
                    ],
                ]);
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
        } catch (\Throwable $e) {
            return $socket->getClientStorage()->send([$id], [
                $this->name => [
                    'message' => 'This game could not be started.',
                ],
            ]);
        }
    }
}
