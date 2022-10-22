<?php

namespace ChessServer\Command;

class PlayUciCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/play_uci';
        $this->description = 'Plays a chess move in UCI format.';
        $this->params = [
            'color' => '<string>',
            'uci' => '<string>',
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
