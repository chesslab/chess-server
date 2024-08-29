<?php

namespace ChessServer\Command\Data;

use ChessServer\Socket\AbstractSocket;

class ResultCommand extends AbstractDataCommand
{
    const RESULT_FILE = 'most_played_openings.json';

    public function __construct(Db $db)
    {
        parent::__construct($db);

        $this->name = '/result';
        $this->description = 'Openings results.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $contents = file_get_contents(AbstractSocket::DATA_FOLDER.'/'.self::RESULT_FILE);

        $arr = json_decode($contents);

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => $arr,
        ]);
    }
}
