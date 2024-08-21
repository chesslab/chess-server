<?php

namespace ChessServer\Socket;

use ChessServer\Command\Parser;

abstract class AbstractSocket
{
    const DATA_FOLDER = __DIR__.'/../../data';

    protected Parser $parser;

    protected TextClientStorageInterface $clientStorage;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;

        echo "Welcome to PHP Chess Server" . PHP_EOL;
        echo "Commands available:" . PHP_EOL;
        echo $this->parser->cli->help() . PHP_EOL;
        echo "Listening to commands..." . PHP_EOL;
    }

    public function init(TextClientStorageInterface $clientStorage): AbstractSocket
    {
        $this->clientStorage = $clientStorage;

        return $this;
    }

    public function getClientStorage(): TextClientStorageInterface
    {
        return $this->clientStorage;
    }

    public function setParser(Parser $parser)
    {
        $this->parser = $parser;
    }
}
