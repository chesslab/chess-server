<?php

namespace ChessServer\Command;

class HeuristicsBarCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/heuristics_bar';
        $this->description = "Takes an expanded heuristic picture of the current position.";
        $this->params = [
            'fen' => '<string>',
            'variant' => '<string>',
        ];
        $this->dependsOn = [
            StartCommand::class,
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }
}
