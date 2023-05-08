<?php

namespace ChessServer;

use Chess\Game;
use Chess\Grandmaster;
use Chess\Movetext;
use Chess\Player\PgnPlayer;
use Chess\Variant\Capablanca80\FEN\StrToBoard as Capablanca80FenStrToBoard;
use Chess\Variant\Chess960\StartPosition;
use Chess\Variant\Chess960\FEN\StrToBoard as Chess960FenStrToBoard;
use Chess\Variant\Classical\FEN\StrToBoard as ClassicalFenStrToBoard;
use Chess\Variant\Classical\FEN\BoardToStr;
use Chess\Variant\Classical\PGN\AN\Color;
use Chess\Variant\Classical\Randomizer\Randomizer;
use Chess\Variant\Classical\Randomizer\Checkmate\TwoBishopsRandomizer;
use Chess\Variant\Classical\Randomizer\Endgame\PawnEndgameRandomizer;
use ChessServer\Command\AcceptPlayRequestCommand;
use ChessServer\Command\CorrespondenceCommand;
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
use ChessServer\Parser\CommandParser;
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

    private $parser;

    private $gm;

    private $correspStore;

    private $clients = [];

    private $gameModes = [];

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();

        $this->log = new Logger($_ENV['BASE_URL']);
        $this->log->pushHandler(new StreamHandler(self::STORAGE_FOLDER.'/pchess.log', Logger::INFO));

        $this->parser = new CommandParser;
        $this->gm = new Grandmaster(self::DATA_FOLDER.'/players.json');

        $databaseDirectory = self::STORAGE_FOLDER;
        $this->correspStore = new \SleekDB\Store("corresp", self::STORAGE_FOLDER);

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

        $gameMode = $this->gameModes[$from->resourceId] ?? null;

        if (is_a($cmd, AcceptPlayRequestCommand::class)) {
            if ($gameMode = $this->gameModeByHash($this->parser->argv[1])) {
                $gameMode->setState(PlayMode::STATE_ACCEPTED);
                if ($this->syncGameModeWith($gameMode, $from)) {
                    $jwt = $gameMode->getJwt();
                    return $this->sendToMany($gameMode->getResourceIds(), [
                        $cmd->name => [
                            'jwt' => $jwt,
                            'hash' => md5($jwt),
                        ],
                    ]);
                }
            }
            return $this->sendToOne($from->resourceId, [
                $cmd->name => [
                    'mode' => PlayMode::NAME,
                    'message' =>  'This friend request could not be accepted.',
                ],
            ]);
        } elseif (is_a($cmd, CorrespondenceCommand::class)) {
            $action = $this->parser->argv[1];
            $variant = $this->parser->argv[2];
            if (CorrespondenceCommand::ACTION_CREATE === $action) {
                $hash = md5(uniqid());
                $add = json_decode(stripslashes($this->parser->argv[3]), true);
                try {
                    if ($variant === Game::VARIANT_960) {
                        $startPos = str_split($corresp['add']['startPos']);
                        $fen = isset($add['fen'])
                            ? $add['fen']
                            : (new \Chess\Variant\Chess960\Board($startPos))->toFen();
                        $board = (new Chess960FenStrToBoard($fen, $startPos))
                            ->create();
                    } elseif ($variant === Game::VARIANT_CAPABLANCA_80) {
                        $fen = isset($add['fen'])
                            ? $add['fen']
                            : (new \Chess\Variant\Capablanca80\Board())->toFen();
                        $board = (new Capablanca80FenStrToBoard($fen))
                            ->create();
                    } else {
                        $fen = isset($add['fen'])
                            ? $add['fen']
                            : (new \Chess\Variant\Classical\Board())->toFen();
                        $board = (new ClassicalFenStrToBoard($fen))
                            ->create();
                    }
                } catch (\Exception $e) {
                    return $this->sendToOne($from->resourceId, [
                        $cmd->name => [
                            'action' => CorrespondenceCommand::ACTION_CREATE,
                            'message' =>  'Invalid FEN, please try again with a different one.',
                        ],
                    ]);
                }
                $corresp = [
                    'hash' => $hash,
                    'variant' => $variant,
                    'add' => $add,
                    'fen' => $board->toFen(),
                    'movetext' => '',
                ];
                $this->correspStore->insert($corresp);
                $res = [
                    $cmd->name => [
                        'action' => CorrespondenceCommand::ACTION_CREATE,
                        'hash' => $hash,
                        'corresp' =>  $corresp,
                    ],
                ];
            } elseif (CorrespondenceCommand::ACTION_READ === $action) {
                if ($corresp = $this->correspStore->findOneBy(['hash', '=', $variant])) {
                    $res = [
                        $cmd->name => [
                            'action' => CorrespondenceCommand::ACTION_READ,
                            'corresp' => $corresp,
                        ],
                    ];
                } else {
                    $res = [
                        $cmd->name => [
                            'action' => CorrespondenceCommand::ACTION_READ,
                            'message' =>  'This correspondence code does not exist.',
                        ],
                    ];
                }
            } elseif (CorrespondenceCommand::ACTION_REPLY === $action) {
                if ($corresp = $this->correspStore->findOneBy(['hash', '=', $variant])) {
                    if (isset($corresp['add']['fen'])) {
                      if ($corresp['variant'] === Game::VARIANT_960) {
                          $startPos = str_split($corresp['add']['startPos']);
                          $board = (new Chess960FenStrToBoard($corresp['add']['fen'], $startPos))
                              ->create();
                      } elseif ($corresp['variant'] === Game::VARIANT_CAPABLANCA_80) {
                          $board = (new Capablanca80FenStrToBoard($corresp['add']['fen']))
                              ->create();
                      } else {
                          $board = (new ClassicalFenStrToBoard($corresp['add']['fen']))
                              ->create();
                      }
                    } else {
                        if ($corresp['variant'] === Game::VARIANT_960) {
                            $startPos = (new StartPosition())->create();
                            $board = new \Chess\Variant\Chess960\Board($startPos);
                        } elseif ($corresp['variant'] === Game::VARIANT_CAPABLANCA_80) {
                            $board = new \Chess\Variant\Capablanca80\Board();
                        } else {
                            $board = new \Chess\Variant\Classical\Board();
                        }
                    }
                    try {
                        $board = (new PgnPlayer($corresp['movetext'], $board))->play()->getBoard();
                        $board->play($board->getTurn(), $this->parser->argv[3]);
                        $corresp['fen'] = $board->toFen();
                        $corresp['movetext'] = $board->getMovetext();
                        $this->correspStore->update($corresp);
                        $res = [
                            $cmd->name => [
                                'action' => CorrespondenceCommand::ACTION_REPLY,
                                'corresp' =>  $corresp,
                            ],
                        ];
                    } catch (\Exception $e) {
                        $res = [
                            $cmd->name => [
                                'action' => CorrespondenceCommand::ACTION_REPLY,
                                'message' =>  'This move is not valid.',
                            ],
                        ];
                    }
                }
            }
            return $this->sendToOne($from->resourceId, $res);
        } elseif (is_a($cmd, DrawCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            }
        } elseif (is_a($cmd, LeaveCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                $this->deleteGameModes($from->resourceId);
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            }
        } elseif (is_a($cmd, OnlineGamesCommand::class)) {
            return $this->sendToOne($from->resourceId, [
                $cmd->name => $this->playModesArrayByState(PlayMode::STATE_PENDING),
            ]);
        } elseif (is_a($cmd, PlayLanCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            } elseif ($gameMode) {
                return $this->sendToOne(
                    $from->resourceId,
                    $this->gameModes[$from->resourceId]->res($this->parser->argv, $cmd)
                );
            }
        } elseif (is_a($cmd, RandomizerCommand::class)) {
            try {
                $items = json_decode(stripslashes($this->parser->argv[2]), true);
                if (count($items) === 1) {
                    $color = array_key_first($items);
                    $ids = str_split(current($items));
                    if ($ids === ['B', 'B']) {
                        $board = (new TwoBishopsRandomizer($this->parser->argv[1]))->getBoard();
                    } elseif ($ids === ['P']) {
                        $board = (new PawnEndgameRandomizer($this->parser->argv[1]))->getBoard();
                    } else {
                        $board = (new Randomizer($this->parser->argv[1], [$color => $ids]))->getBoard();
                    }
                } else {
                    $wIds = str_split($items[Color::W]);
                    $bIds = str_split($items[Color::B]);
                    $board = (new Randomizer($this->parser->argv[1], [
                        Color::W => $wIds,
                        Color::B => $bIds,
                    ]))->getBoard();
                }
                $res = [
                    $cmd->name => [
                        'turn' => $board->getTurn(),
                        'fen' => (new BoardToStr($board))->create(),
                    ],
                ];
            } catch (\Throwable $e) {
                $res = [
                    $cmd->name => [
                        'message' => 'A random puzzle could not be loaded.',
                    ],
                ];
            }
            return $this->sendToOne($from->resourceId, $res);
        } elseif (is_a($cmd, RematchCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            }
        } elseif (is_a($cmd, ResignCommand::class)) {
            if (is_a($gameMode, PlayMode::class)) {
                return $this->sendToMany(
                    $gameMode->getResourceIds(),
                    $gameMode->res($this->parser->argv, $cmd)
                );
            }
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
                $res = [
                    $cmd->name => [
                        'variant' => $variant,
                        'mode' => $mode,
                        'fen' => $analysisMode->getGame()->getBoard()->toFen(),
                        ...($variant === Game::VARIANT_960
                            ? ['startPos' => implode('', $analysisMode->getGame()->getBoard()->getStartPos())]
                            : []
                        ),
                    ],
                ];
            } elseif (GmMode::NAME === $mode) {
                $this->gameModes[$from->resourceId] = new GmMode(
                    new Game($variant, $mode, $this->gm),
                    [$from->resourceId]
                );
                $res = [
                    $cmd->name => [
                        'variant' => $variant,
                        'mode' => $mode,
                        'color' => $this->parser->argv[3],
                    ],
                ];
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
                    $res = [
                        $cmd->name => [
                            'variant' => $variant,
                            'mode' => $mode,
                            'fen' => $this->parser->argv[3],
                            ...($variant === Game::VARIANT_960
                                ? ['startPos' =>  $this->parser->argv[4]]
                                : []
                            ),
                        ],
                    ];
                } catch (\Throwable $e) {
                    $res = [
                        $cmd->name => [
                            'variant' => $variant,
                            'mode' => $mode,
                            'message' => 'This FEN string could not be loaded.',
                        ],
                    ];
                }
            } elseif (PgnMode::NAME === $mode) {
                try {
                    if ($variant === Game::VARIANT_960) {
                        $move = new \Chess\Variant\Classical\PGN\Move();
                        $movetext = (new Movetext($move, $this->parser->argv[3]))->validate();
                        $startPos = str_split($this->parser->argv[4]);
                        $board = new \Chess\Variant\Chess960\Board($startPos);
                        $player = (new PgnPlayer($movetext, $board))->play();
                    } elseif ($variant === Game::VARIANT_CAPABLANCA_80) {
                        $move = new \Chess\Variant\Capablanca80\PGN\Move();
                        $movetext = (new Movetext($move, $this->parser->argv[3]))->validate();
                        $board = new \Chess\Variant\Capablanca80\Board();
                        $player = (new PgnPlayer($movetext, $board))->play();
                    } else {
                        $move = new \Chess\Variant\Classical\PGN\Move();
                        $movetext = (new Movetext($move, $this->parser->argv[3]))->validate();
                        $player = (new PgnPlayer($movetext))->play();
                    }
                    $pgnMode = new PgnMode(new Game($variant, $mode), [$from->resourceId]);
                    $game = $pgnMode->getGame()->setBoard($player->getBoard());
                    $pgnMode->setGame($game);
                    $this->gameModes[$from->resourceId] = $pgnMode;
                    $res = [
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
                    ];
                } catch (\Throwable $e) {
                    $res = [
                        $cmd->name => [
                            'variant' => $variant,
                            'mode' => $mode,
                            'message' => 'This PGN movetext could not be loaded.',
                        ],
                    ];
                }
            } elseif (PlayMode::NAME === $mode) {
                $res = [];
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
                        $res = [
                            $cmd->name => [
                                'variant' => $variant,
                                'mode' => $mode,
                                'message' => 'This FEN string could not be loaded.',
                            ],
                        ];
                    }
                } else {
                    if ($variant === Game::VARIANT_960) {
                        $startPos = (new StartPosition())->create();
                        $board = new \Chess\Variant\Chess960\Board($startPos);
                    } elseif ($variant === Game::VARIANT_CAPABLANCA_80) {
                        $board = new \Chess\Variant\Capablanca80\Board();
                    } else {
                        $board = new \Chess\Variant\Classical\Board();
                    }
                }
                if (!$res) {
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
                    $res = [
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
                    ];
                    if ($settings->submode === PlayMode::SUBMODE_ONLINE) {
                        $this->broadcast();
                    }
                }
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
                    $res = [
                        $cmd->name => [
                            'variant' => $variant,
                            'mode' => $mode,
                            'color' => $game->getBoard()->getTurn(),
                            'fen' => $game->getBoard()->toFen(),
                        ],
                    ];
                } catch (\Throwable $e) {
                    if ($this->parser->argv[3] === Color::W || $this->parser->argv[3] === Color::B) {
                        $stockfishMode = new StockfishMode(
                            new Game($variant, $mode, $this->gm),
                            [$from->resourceId]
                        );
                        $this->gameModes[$from->resourceId] = $stockfishMode;
                        $res = [
                            $cmd->name => [
                                'variant' => $variant,
                                'mode' => $mode,
                                'color' => $this->parser->argv[3],
                            ],
                        ];
                    } else {
                        $res = [
                            $cmd->name => [
                                'variant' => $variant,
                                'mode' => $mode,
                                'message' => 'Stockfish could not be started.',
                            ],
                        ];
                    }
                }
            }
            return $this->sendToOne($from->resourceId, $res);
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

    protected function gameModeByHash(string $hash)
    {
        foreach ($this->gameModes as $gameMode) {
            if ($hash === $gameMode->getHash()) {
                return $gameMode;
            }
        }

        return null;
    }

    protected function playModesArrayByState(string $state)
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

    protected function gameModeByResourceId(int $id)
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

    protected function deleteGameModes(int $resourceId)
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

    protected function syncGameModeWith(AbstractMode $gameMode, ConnectionInterface $from)
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

    protected function sendToOne(int $resourceId, array $res)
    {
        if (isset($this->clients[$resourceId])) {
            $this->clients[$resourceId]->send(json_encode($res));

            $this->log->info('Sent message', [
                'id' => $resourceId,
                'cmd' => array_keys($res),
            ]);
        }
    }

    protected function sendToMany(array $resourceIds, array $res)
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
