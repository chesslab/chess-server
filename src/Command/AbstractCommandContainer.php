<?php

namespace ChessServer\Command;

use \SplObjectStorage;

abstract class AbstractCli
{
    protected SplObjectStorage $commands;

    public function __construct()
    {
        $this->commands = new SplObjectStorage;
    }

    public function findByName(string $name)
    {
        $this->commands->rewind();
        while ($this->commands->valid()) {
            if ($this->commands->current()->name === $name) {
                return $this->commands->current();
            }
            $this->commands->next();
        }

        return null;
    }

    public function help()
    {
        $help = '';
        $this->commands->rewind();
        while ($this->commands->valid()) {
            $help .= $this->commands->current()->name;
            $this->commands->current()->params
                ? $help .= ' ' . json_encode($this->commands->current()->params)
                : null;
            $help .= ' ' . $this->commands->current()->description . PHP_EOL;
            $this->commands->next();
        }

        return $help;
    }
}
