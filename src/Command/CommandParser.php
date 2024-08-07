<?php

namespace ChessServer\Command;

use ChessServer\Exception\ParserException;

class CommandParser
{
    protected $argv;

    public function __construct(protected AbstractCommandContainer $cli)
    {}

    public function __get($property)
    {
        return $this->$property ?? null;
    }

    public function validate($string)
    {
        $this->argv = $this->filter($string);
        $command = $this->cli->findByName($this->argv[0]);
        if (!$command || !$command->validate($this->argv)) {
            throw new ParserException();
        }

        return $command;
    }

    protected function filter($string)
    {
        return array_map('trim', str_getcsv($string, ' '));
    }
}
