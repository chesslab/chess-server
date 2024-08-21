<?php

namespace ChessServer\Command\Binary;

use Chess\Media\BoardToPng;
use Chess\Variant\Classical\FEN\StrToBoard as ClassicalStrToBoard;
use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\AbstractSocket;

class ImageCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/image';
        $this->description = 'Transmits an image.';
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
        $filename = (new BoardToPng($board, $params['flip']))->output(AbstractSocket::TMP_FOLDER);
        $contents = file_get_contents(AbstractSocket::TMP_FOLDER . "/$filename");
        $base64 = base64_encode($contents);

        return $socket->getClientStorage()->sendToOne($id, $base64);
    }
}
