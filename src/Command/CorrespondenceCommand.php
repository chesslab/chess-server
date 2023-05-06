<?php

namespace ChessServer\Command;

class CorrespondenceCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/correspondence';
        $this->description = "Returns a correspondence game.";
        $this->params = [
            'hash' => '<string>',
            'pgn' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }
}
