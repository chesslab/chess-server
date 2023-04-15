<?php

namespace ChessServer\Command;

class LegalCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/legal';
        $this->description = 'Returns the legal FEN positions of a piece.';
        $this->params = [
            'position' => '<string>',
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
