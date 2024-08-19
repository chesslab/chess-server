<?php

namespace ChessServer\Command;

use ChessServer\Exception\ParserException;

class Parser
{
    protected $argv;

    protected $cli;

    public function __construct(AbstractCli $cli)
    {
        $this->cli = $cli;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
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
