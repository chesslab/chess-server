<?php

namespace ChessServer\Socket\Ratchet;

use ChessServer\Command\CommandParser;
use ChessServer\Exception\ParserException;
use ChessServer\Socket\AbstractChesslaBlabSocket;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\Factory;
use React\EventLoop\StreamSelectLoop;

abstract class AbstractWebSocket extends AbstractChesslaBlabSocket implements MessageComponentInterface
{
    protected StreamSelectLoop $loop;

    public function __construct(CommandParser $parser)
    {
        parent::__construct($parser);

        $this->loop = Factory::create();
    }

    public function getLoop()
    {
        return $this->loop;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clientStorage->attach($conn);

        $this->clientStorage->getLogger()->info('New connection', [
            'id' => $conn->resourceId,
            'n' => $this->clientStorage->count()
        ]);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if (strlen($msg) > 4096) {
            return $this->clientStorage->sendToOne($from->resourceId, [
                'error' => 'Internal server error',
            ]);
        }

        try {
            $cmd = $this->parser->validate($msg);
        } catch (ParserException $e) {
            return $this->clientStorage->sendToOne($from->resourceId, [
                'error' => 'Command parameters not valid',
            ]);
        }

        try {
            $cmd->run($this, $this->parser->argv, $from->resourceId);
        } catch (\Throwable $e) {
            $this->clientStorage->getLogger()->error('Occurred an error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->clientStorage->sendToOne($from->resourceId, [
                'error' => 'Internal server error',
            ]);
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();

        $this->clientStorage->getLogger()->info('Occurred an error', ['message' => $e->getMessage()]);
    }
}
