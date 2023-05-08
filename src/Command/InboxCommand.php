<?php

namespace ChessServer\Command;

use Chess\Game;
use Chess\Variant\Classical\PGN\AN\Color;

class InboxCommand extends AbstractCommand
{
    const ACTION_CREATE = 'create';

    const ACTION_READ = 'read';

    const ACTION_REPLY = 'reply';

    public function __construct()
    {
        $this->name = '/inbox';
        $this->description = "Correspondence chess.";
        $this->params = [
            // mandatory
            'action' => [
                self::ACTION_CREATE,
                self::ACTION_READ,
                self::ACTION_REPLY,
            ],
            // optional
            'variant' => [
                Game::VARIANT_960,
                Game::VARIANT_CAPABLANCA_80,
                Game::VARIANT_CAPABLANCA_100,
                Game::VARIANT_CLASSICAL,
            ],
            // optional
            'settings' => [
                'fen' => '<string>',
                'movetext' => '<string>',
                'startPos' => '<string>',
            ],
            // optional
            'hash' => '<string>',
            // optional
            'movetext' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        if (in_array($argv[1], $this->params['action'])) {
            return count($argv) - 1 === count($this->params) - 2 ||
                count($argv) - 1 === count($this->params) - 3;
        }

        return false;
    }
}
