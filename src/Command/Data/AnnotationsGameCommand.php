<?php

namespace ChessServer\Command\Data;

use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\ChesslaBlabSocket;

class AnnotationsGameCommand extends AbstractCommand
{
    const DATA_FOLDER = __DIR__.'/../../../data';

    const ANNOTATIONS_GAMES_FILE = 'annotations_games.json';

    public function __construct()
    {
        $this->name = '/annotations_game';
        $this->description = 'Annotated chess games available in the database.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(ChesslaBlabSocket $socket, array $argv, int $id)
    {
        $contents = file_get_contents(self::DATA_FOLDER.'/'.self::ANNOTATIONS_GAMES_FILE);

        $arr = json_decode($contents);

        return $socket->getClientStorage()->sendToOne($id, $arr);
    }
}
