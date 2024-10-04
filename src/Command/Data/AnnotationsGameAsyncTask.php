<?php

namespace ChessServer\Command\Data;

use ChessServer\Socket\AbstractSocket;
use Spatie\Async\Task;

class AnnotationsGameAsyncTask extends Task
{
    const ANNOTATIONS_GAMES_FILE = 'annotations_games.json';

    public function configure()
    {
    }

    public function run()
    {
        $contents = file_get_contents(AbstractSocket::DATA_FOLDER.'/'.self::ANNOTATIONS_GAMES_FILE);

        return json_decode($contents);
    }
}
