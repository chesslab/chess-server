<?php

namespace ChessServer\Command\Binary;

use Chess\Media\BoardToPng;
use Chess\Variant\Classical\FEN\StrToBoard as ClassicalStrToBoard;
use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\AbstractSocket;

class TransmitCommand extends AbstractCommand
{
    const OUTPUT_FOLDER = __DIR__.'/../../../storage/tmp';

    public function __construct()
    {
        $this->name = '/transmit';
        $this->description = 'Transmission of binary data.';
        $this->params = [
            'settings' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $params = json_decode(stripslashes($argv[1]), true);

        $board = (new ClassicalStrToBoard($params['fen']))->create();
        $filename = (new BoardToPng($board, $params['flip']))->output(self::OUTPUT_FOLDER);
        $contents = file_get_contents(self::OUTPUT_FOLDER . "/$filename");
        $base64 = base64_encode($contents);

        return $socket->getClientStorage()->sendToOne($id, $base64);
    }
}
