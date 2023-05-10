<?php

namespace ChessServer;

use Chess\Game;
use Chess\Grandmaster;
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
use ChessServer\Command\AcceptPlayRequestCommand;
use ChessServer\Command\InboxCommand;
use ChessServer\Command\DrawCommand;
use ChessServer\Command\LeaveCommand;
use ChessServer\Command\OnlineGamesCommand;
use ChessServer\Command\PlayLanCommand;
use ChessServer\Command\RandomizerCommand;
use ChessServer\Command\RematchCommand;
use ChessServer\Command\ResignCommand;
use ChessServer\Command\RestartCommand;
use ChessServer\Command\StartCommand;
use ChessServer\Command\TakebackCommand;
use ChessServer\Command\UndoCommand;
use ChessServer\Exception\ParserException;
use ChessServer\GameMode\AbstractMode;
use ChessServer\GameMode\AnalysisMode;
use ChessServer\GameMode\GmMode;
use ChessServer\GameMode\FenMode;
use ChessServer\GameMode\PgnMode;
use ChessServer\GameMode\PlayMode;
use ChessServer\GameMode\StockfishMode;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Socket implements MessageComponentInterface
{
    const DATA_FOLDER = __DIR__.'/../data';

    const STORAGE_FOLDER = __DIR__.'/../storage';

    private $log;

    private $cli;

    private $parser;

    private $gm;

    private $inboxStore;

    private $clients = [];

    private $gameModes = [];

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();

        $this->log = new Logger($_ENV['BASE_URL']);
        $this->log->pushHandler(new StreamHandler(self::STORAGE_FOLDER.'/pchess.log', Logger::INFO));

        $this->cli = new CommandContainer;
        $this->parser = new CommandParser($this->cli);

        $this->gm = new Grandmaster(self::DATA_FOLDER.'/players.json');

        $databaseDirectory = self::STORAGE_FOLDER;
        $this->inboxStore = new \SleekDB\Store("inbox", self::STORAGE_FOLDER);

        echo "Welcome to PHP Chess Server" . PHP_EOL;
        echo "Commands available:" . PHP_EOL;
        echo $this->parser->cli->help() . PHP_EOL;
        echo "Listening to commands..." . PHP_EOL;

        $this->log->info('Started the chess server');
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = $conn;
        $this->log->info('New connection', [
            'id' => $conn->resourceId,
            'n' => count($this->clients)
        ]);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $cmd = $this->parser->validate($msg);
        } catch (ParserException $e) {
            return $this->sendToOne($from->resourceId, [
                'error' => $e->getMessage(),
            ]);
        }

        $gameMode = $this->gameModeByResourceId($from->resourceId);

        if (is_a($cmd, AcceptPlayRequestCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, DrawCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, InboxCommand::class)) {
            $action = $this->parser->argv[1];
            if (InboxCommand::ACTION_CREATE === $action) {
                $variant = $this->parser->argv[2];
                $hash = md5(uniqid());
                $settings = json_decode(stripslashes($this->parser->argv[3]), true);
                try {
                    if ($variant === Game::VARIANT_960) {
                        $startPos = str_split($settings['startPos']);
                        $fen = $settings['fen'] ?? (new Chess960Board($startPos))->toFen();
                        $board = (new Chess960FenStrToBoard($fen, $startPos))->create();
                    } elseif ($variant === Game::VARIANT_CAPABLANCA_80) {
                        $fen = $settings['fen'] ?? (new Capablanca80Board())->toFen();
                        $board = (new Capablanca80FenStrToBoard($fen))->create();
                    } else {
                        $fen = $settings['fen'] ?? (new ClassicalBoard())->toFen();
                        $board = (new ClassicalFenStrToBoard($fen))->create();
                    }
                } catch (\Exception $e) {
                    return $this->sendToOne($from->resourceId, [
                        $cmd->name => [
                            'action' => InboxCommand::ACTION_CREATE,
                            'message' =>  'Invalid FEN, please try again with a different one.',
                        ],
                    ]);
                }
                $inbox = [
                    'hash' => $hash,
                    'variant' => $variant,
                    'settings' => $settings,
                    'fen' => $board->toFen(),
                    'movetext' => '',
                    'createdAt' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'createdBy' => $from->resourceId,
                ];
                $this->inboxStore->insert($inbox);
                return $this->sendToOne($from->resourceId, [
                    $cmd->name => [
                        'action' => InboxCommand::ACTION_CREATE,
                        'hash' => $hash,
                        'inbox' =>  $inbox,
                    ],
                ]);
            } elseif (InboxCommand::ACTION_READ === $action) {
                $hash = $this->parser->argv[2];
                if ($inbox = $this->inboxStore->findOneBy(['hash', '=', $hash])) {
                    return $this->sendToOne($from->resourceId, [
                        $cmd->name => [
                            'action' => InboxCommand::ACTION_READ,
                            'inbox' => $inbox,
                        ],
                    ]);
                } else {
                    return $this->sendToOne($from->resourceId, [
                        $cmd->name => [
                            'action' => InboxCommand::ACTION_READ,
                            'message' =>  'This inbox code does not exist.',
                        ],
                    ]);
                }
            } elseif (InboxCommand::ACTION_REPLY === $action) {
                $hash = $this->parser->argv[2];
                if ($inbox = $this->inboxStore->findOneBy(['hash', '=', $hash])) {
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
                        $board->play($board->getTurn(), $this->parser->argv[3]);
                        $inbox['fen'] = $board->toFen();
                        $inbox['movetext'] = $board->getMovetext();
                        $inbox['updatedAt'] = (new \DateTime())->format('Y-m-d H:i:s');
                        $inbox['updatedBy'] = $from->resourceId;
                        $this->inboxStore->update($inbox);
                        return $this->sendToOne($from->resourceId, [
                            $cmd->name => [
                                'action' => InboxCommand::ACTION_REPLY,
                                'message' =>  'Chess move successfully sent.',
                            ],
                        ]);
                    } catch (\Exception $e) {
                        return $this->sendToOne($from->resourceId, [
                            $cmd->name => [
                                'action' => InboxCommand::ACTION_REPLY,
                                'message' =>  'Invalid PGN move, please try again with a different one.',
                            ],
                        ]);
                    }
                }
            }
        } elseif (is_a($cmd, LeaveCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, OnlineGamesCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, PlayLanCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, RandomizerCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, RematchCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, ResignCommand::class)) {
            $cmd->run($this, $this->parser->argv, $from);
        } elseif (is_a($cmd, RestartCommand::class)) {
            if ($gameMode = $this->gameModeByHash($this->parser->argv[1])) {
                $jwt = $gameMode->getJwt();
                $decoded = JWT::decode($jwt, $_ENV['JWT_SECRET'], array('HS256'));
                $decoded->iat = time();
                $decoded->exp = time() + 3600; // one hour by default
                $newJwt = JWT::encode($decoded, $_ENV['JWT_SECRET']);
                $resourceIds = $gameMode->getResourceIds();
                $newGameMode = new PlayMode(
                    new Game(Game::VARIANT_CLASSICAL, Game::MODE_PLAY),
                    [$resourceIds[0], $resourceIds[1]],
                    $newJwt
                );
                $newGameMode->setState(PlayMode::STATE_ACCEPTED);
                $this->gameModes[$resourceIds[0]] = $newGameMode;
                $this->gameModes[$resourceIds[1]] = $newGameMode;
                return $this->sendToMany($newGameMode->getResourceIds(), [
                    $cmd->name => [
                        'jwt' => $newJwt,
                        'hash' => md5($newJwt),
                    ],
                ]);
            }
        } elseif (is_a($cmd, StartCommand::class)) {
            $variant = $this->parser->argv[1];
            $mode = $this->parser->argv[2];
            if (AnalysisMode::NAME === $mode) {
                $analysisMode = new AnalysisMode(
                    new Game($variant, $mode),
                    [$from->resourceId]
                );
                $this->gameModes[$from->resourceId] = $analysisMode;
                return $this->sendToOne($from->resourceId, [
                    $cmd->name => [
                        'variant' => $variant,
                        'mode' => $mode,
                        'fen' => $analysisMode->getGame()->getBoard()->toFen(),
                        ...($variant === Game::VARIANT_960
                            ? ['startPos' => implode('', $analysisMode->getGame()->getBoard()->getStartPos())]
                            : []
                        ),
                    ],
                ]);
            } elseif (GmMode::NAME === $mode) {
                $this->gameModes[$from->resourceId] = new GmMode(
                    new Game($variant, $mode, $this->gm),
                    [$from->resourceId]
                );
                return $this->sendToOne($from->resourceId, [
                    $cmd->name => [
                        'variant' => $variant,
                        'mode' => $mode,
                        'color' => $this->parser->argv[3],
                    ],
                ]);
            } elseif (FenMode::NAME === $mode) {
                try {
                    if ($variant === Game::VARIANT_960) {
                        $startPos = str_split($this->parser->argv[4]);
                        $board = (new Chess960FenStrToBoard($this->parser->argv[3], $startPos))
                            ->create();
                    } elseif ($variant === Game::VARIANT_CAPABLANCA_80) {
                        $board = (new Capablanca80FenStrToBoard($this->parser->argv[3]))
                            ->create();
                    } else {
                        $board = (new ClassicalFenStrToBoard($this->parser->argv[3]))
                            ->create();
                    }
                    $fenMode = new FenMode(
                        new Game($variant, $mode),
                        [$from->resourceId],
                        $this->parser->argv[3]
                    );
                    $fenMode->getGame()->setBoard($board);
                    $this->gameModes[$from->resourceId] = $fenMode;
                    return $this->sendToOne($from->resourceId, [
                        $cmd->name => [
                            'variant' => $variant,
                            'mode' => $mode,
                            'fen' => $this->parser->argv[3],
                            ...($variant === Game::VARIANT_960
                                ? ['startPos' =>  $this->parser->argv[4]]
                                : []
                            ),
                        ],
                    ]);
                } catch (\Throwable $e) {
                    return $this->sendToOne($from->resourceId, [
                        $cmd->name => [
                            'variant' => $variant,
                            'mode' => $mode,
                            'message' => 'This FEN string could not be loaded.',
                        ],
                    ]);
                }
            } elseif (PgnMode::NAME === $mode) {
                try {
                    if ($variant === Game::VARIANT_960) {
                        $move = new ClassicalPgnMove();
                        $movetext = (new Movetext($move, $this->parser->argv[3]))->validate();
                        $startPos = str_split($this->parser->argv[4]);
                        $board = new Chess960Board($startPos);
                        $player = (new PgnPlayer($movetext, $board))->play();
                    } elseif ($variant === Game::VARIANT_CAPABLANCA_80) {
                        $move = new Capablanca80PgnMove();
                        $movetext = (new Movetext($move, $this->parser->argv[3]))->validate();
                        $board = new Capablanca80Board();
                        $player = (new PgnPlayer($movetext, $board))->play();
                    } else {
                        $move = new ClassicalPgnMove();
                        $movetext = (new Movetext($move, $this->parser->argv[3]))->validate();
                        $player = (new PgnPlayer($movetext))->play();
                    }
                    $pgnMode = new PgnMode(new Game($variant, $mode), [$from->resourceId]);
                    $game = $pgnMode->getGame()->setBoard($player->getBoard());
                    $pgnMode->setGame($game);
                    $this->gameModes[$from->resourceId] = $pgnMode;
                    return $this->sendToOne($from->resourceId, [
                        $cmd->name => [
                            'variant' => $variant,
                            'mode' => $mode,
                            'turn' => $game->state()->turn,
                            'movetext' => $movetext,
                            'fen' => $player->getFen(),
                            ...($variant === Game::VARIANT_960
                                ? ['startPos' =>  $this->parser->argv[4]]
                                : []
                            ),
                        ],
                    ]);
                } catch (\Throwable $e) {
                    return $this->sendToOne($from->resourceId, [
                        $cmd->name => [
                            'variant' => $variant,
                            'mode' => $mode,
                            'message' => 'This PGN movetext could not be loaded.',
                        ],
                    ]);
                }
            } elseif (PlayMode::NAME === $mode) {
                $settings = (object) json_decode(stripslashes($this->parser->argv[3]), true);
                if (isset($settings->fen)) {
                    try {
                        if ($variant === Game::VARIANT_960) {
                            $startPos = str_split($settings->startPos);
                            $board = (new Chess960FenStrToBoard($settings->fen, $startPos))
                                ->create();
                        } elseif ($variant === Game::VARIANT_CAPABLANCA_80) {
                            $board = (new Capablanca80FenStrToBoard($settings->fen))
                                ->create();
                        } else {
                            $board = (new ClassicalFenStrToBoard($settings->fen))
                                ->create();
                        }
                    } catch (\Throwable $e) {
                        return $this->sendToOne($from->resourceId, [
                            $cmd->name => [
                                'variant' => $variant,
                                'mode' => $mode,
                                'message' => 'This FEN string could not be loaded.',
                            ],
                        ]);
                    }
                } else {
                    if ($variant === Game::VARIANT_960) {
                        $startPos = (new StartPosition())->create();
                        $board = new Chess960Board($startPos);
                    } elseif ($variant === Game::VARIANT_CAPABLANCA_80) {
                        $board = new Capablanca80Board();
                    } else {
                        $board = new ClassicalBoard();
                    }
                }
                $game = (new Game($variant, $mode))->setBoard($board);
                $payload = [
                    'iss' => $_ENV['JWT_ISS'],
                    'iat' => time(),
                    'exp' => time() + 3600, // one hour by default
                    'variant' => $this->parser->argv[1],
                    'submode' => $settings->submode,
                    'color' => $settings->color,
                    'min' => $settings->min,
                    'increment' => $settings->increment,
                    'fen' => $game->getBoard()->toFen(),
                    ...($variant === Game::VARIANT_960
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
                $this->gameModes[$from->resourceId] = $playMode;
                if ($settings->submode === PlayMode::SUBMODE_ONLINE) {
                    $this->broadcast();
                }
                return $this->sendToOne($from->resourceId, [
                    $cmd->name => [
                        'variant' => $variant,
                        'mode' => $mode,
                        'fen' => $game->getBoard()->toFen(),
                        'jwt' => $jwt,
                        'hash' => md5($jwt),
                        ...($variant === Game::VARIANT_960
                            ? ['startPos' =>  implode('', $game->getBoard()->getStartPos())]
                            : []
                        ),
                    ],
                ]);
            } elseif (StockfishMode::NAME === $mode) {
                try {
                    $stockfishMode = new StockfishMode(
                        new Game($variant, $mode),
                        [$from->resourceId],
                        $this->parser->argv[3]
                    );
                    $game = $stockfishMode->getGame();
                    $game->loadFen($this->parser->argv[3]);
                    $stockfishMode->setGame($game);
                    $this->gameModes[$from->resourceId] = $stockfishMode;
                    return $this->sendToOne($from->resourceId, [
                        $cmd->name => [
                            'variant' => $variant,
                            'mode' => $mode,
                            'color' => $game->getBoard()->getTurn(),
                            'fen' => $game->getBoard()->toFen(),
                        ],
                    ]);
                } catch (\Throwable $e) {
                    if ($this->parser->argv[3] === Color::W || $this->parser->argv[3] === Color::B) {
                        $stockfishMode = new StockfishMode(
                            new Game($variant, $mode, $this->gm),
                            [$from->resourceId]
                        );
                        $this->gameModes[$from->resourceId] = $stockfishMode;
                        return $this->sendToOne($from->resourceId, [
                            $cmd->name => [
                                'variant' => $variant,
                                'mode' => $mode,
                                'color' => $this->parser->argv[3],
                            ],
                        ]);
                    } else {
                        return $this->sendToOne($from->resourceId, [
                            $cmd->name => [
                                'variant' => $variant,
                                'mode' => $mode,
                                'message' => 'Stockfish could not be started.',
                            ],
                        ]);
                    }
                }
            }
        } elseif (is_a($cmd, TakebackCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            }
        } elseif (is_a($cmd, UndoCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            } elseif (
                is_a($gameMode, AnalysisMode::class) ||
                is_a($gameMode, FenMode::class) ||
                is_a($gameMode, GmMode::class) ||
                is_a($gameMode, PgnMode::class) ||
                is_a($gameMode, StockfishMode::class)
            ) {
                return $this->sendToOne(
                    $from->resourceId,
                    $gameMode->res($this->parser->argv, $cmd)
                );
            }
        } elseif ($gameMode) {
            return $this->sendToOne(
                $from->resourceId,
                $this->gameModes[$from->resourceId]->res($this->parser->argv, $cmd)
            );
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->leaveGame($conn->resourceId);
        $this->deleteGameModes($conn->resourceId);
        $this->deleteClient($conn->resourceId);

        $this->log->info('Closed connection', [
            'id' => $conn->resourceId,
            'n' => count($this->clients)
        ]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();

        $this->log->info('Occurred an error', ['message' => $e->getMessage()]);
    }

    public function gameModeByHash(string $hash)
    {
        foreach ($this->gameModes as $gameMode) {
            if ($hash === $gameMode->getHash()) {
                return $gameMode;
            }
        }

        return null;
    }

    public function playModesArrayByState(string $state)
    {
        $result = [];
        foreach ($this->gameModes as $gameMode) {
          if (is_a($gameMode, PlayMode::class)) {
            if ($gameMode->getState() === $state) {
                $decoded = JWT::decode($gameMode->getJwt(), $_ENV['JWT_SECRET'], array('HS256'));
                if ($decoded->submode === PlayMode::SUBMODE_ONLINE) {
                    $decoded->hash = $gameMode->getHash();
                    $result[] = $decoded;
                }
            }
          }
        }

        return $result;
    }

    public function gameModeByResourceId(int $id)
    {
        foreach ($this->gameModes as $key => $val) {
            if ($key === $id) {
                return $val;
            }
        }

        return null;
    }

    protected function leaveGame(int $resourceId)
    {
        if ($gameMode = $this->gameModeByResourceId($resourceId)) {
            $toId = null;
            $resourceIds = $gameMode->getResourceIds();
            if ($resourceIds[0] !== $resourceId) {
                $toId = $resourceIds[0];
            } elseif (isset($resourceIds[1]) && $resourceIds[1] !== $resourceId) {
                $toId = $resourceIds[1];
            }
            if ($toId) {
                $this->sendToOne($toId, ['/leave' => LeaveCommand::ACTION_ACCEPT]);
            }
        }
    }

    public function deleteGameModes(int $resourceId)
    {
        if ($gameMode = $this->gameModeByResourceId($resourceId)) {
            $resourceIds = $gameMode->getResourceIds();
            if (isset($resourceIds[0])) {
                if (isset($this->gameModes[$resourceIds[0]])) {
                    unset($this->gameModes[$resourceIds[0]]);
                }
            }
            if (isset($resourceIds[1])) {
                if (isset($this->gameModes[$resourceIds[1]])) {
                    unset($this->gameModes[$resourceIds[1]]);
                }
            }
        }
    }

    protected function deleteClient(int $resourceId)
    {
        if (isset($this->clients[$resourceId])) {
            unset($this->clients[$resourceId]);
        }
    }

    public function syncGameModeWith(AbstractMode $gameMode, ConnectionInterface $from)
    {
        if ($resourceIds = $gameMode->getResourceIds()) {
            if (count($resourceIds) === 1) {
                $resourceIds[] = $from->resourceId;
                $gameMode->setResourceIds($resourceIds);
                foreach ($resourceIds as $resourceId) {
                    $this->gameModes[$resourceId] = $gameMode;
                }
                return true;
            }
        }

        return false;
    }

    public function sendToOne(int $resourceId, array $res)
    {
        if (isset($this->clients[$resourceId])) {
            $this->clients[$resourceId]->send(json_encode($res));

            $this->log->info('Sent message', [
                'id' => $resourceId,
                'cmd' => array_keys($res),
            ]);
        }
    }

    public function sendToMany(array $resourceIds, array $res)
    {
        foreach ($resourceIds as $resourceId) {
            $this->clients[$resourceId]->send(json_encode($res));
        }

        $this->log->info('Sent message', [
            'ids' => $resourceIds,
            'cmd' => array_keys($res),
        ]);
    }

    protected function broadcast()
    {
        $message = [
            'broadcast' => [
                'onlineGames' => $this->playModesArrayByState(PlayMode::STATE_PENDING),
            ],
        ];

        foreach ($this->clients as $client) {
            $client->send(json_encode($message));
        }
    }
}
