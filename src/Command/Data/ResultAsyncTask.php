<?php

namespace ChessServer\Command\Data;

use ChessServer\Socket\AbstractSocket;
use Spatie\Async\Task;

class ResultAsyncTask extends Task
{
    const RESULT_FILE = 'most_played_openings.json';
    
    public function configure()
    {
    }

    public function run()
    {
        $contents = file_get_contents(AbstractSocket::DATA_FOLDER.'/'.self::RESULT_FILE);

        return json_decode($contents);
    }
}
