<?php

namespace ChessServer\Socket\Ratchet;

use ChessServer\Command\Parser;
use ChessServer\Exception\ParserException;
use ChessServer\Socket\AbstractSocket;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\Factory;
use React\EventLoop\StreamSelectLoop;

abstract class AbstractWebSocket extends AbstractSocket implements MessageComponentInterface
{
    protected StreamSelectLoop $loop;

    public function __construct(Parser $parser)
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
            'n' => $this->clientStorage->count(),
        ]);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if (strlen($msg) > 128000) {
            return $this->clientStorage->send([$from->resourceId], [
                'error' => 'Internal server error',
            ]);
        }

        try {
            $cmd = $this->parser->validate($msg);
        } catch (ParserException $e) {
            return $this->clientStorage->send([$from->resourceId], [
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
            return $this->clientStorage->send([$from->resourceId], [
                'error' => 'Internal server error',
            ]);
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
        $this->clientStorage->getLogger()->info('Occurred an error', [
            'message' => $e->getMessage(),
        ]);
    }
}
