<?php

namespace ChessServer\Socket;

use ChessServer\Command\Parser;

abstract class AbstractSocket
{
    const DATA_FOLDER = __DIR__ . '/../../data';

    const STORAGE_FOLDER = __DIR__ . '/../../storage';

    const TMP_FOLDER = __DIR__ . '/../../storage/tmp';

    protected Parser $parser;

    protected ClientStorageInterface $clientStorage;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;

        echo "Welcome to PHP Chess Server" . PHP_EOL;
        echo "Commands available:" . PHP_EOL;
        echo $this->parser->cli->help() . PHP_EOL;
        echo "Listening to commands..." . PHP_EOL;
    }

    public function init(ClientStorageInterface $clientStorage): AbstractSocket
    {
        $this->clientStorage = $clientStorage;

        return $this;
    }

    public function getClientStorage(): ClientStorageInterface
    {
        return $this->clientStorage;
    }

    public function setParser(Parser $parser)
    {
        $this->parser = $parser;
    }
}
