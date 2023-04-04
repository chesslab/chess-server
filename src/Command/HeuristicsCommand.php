<?php

namespace ChessServer\Command;

class HeuristicsCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/heuristics';
        $this->description = "Takes a balanced heuristic picture of the current game.";
        $this->params = [
            'movetext' => '<string>',
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
