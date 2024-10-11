<?php

namespace ChessServer\Command\Binary\Async;

use Chess\Media\BoardToPng;
use Chess\Variant\Classical\FEN\StrToBoard as ClassicalStrToBoard;
use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Socket\AbstractSocket;
use Spatie\Async\Task;

class ImageTask extends Task
{
    private array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function configure()
    {
    }

    public function run()
    {
        $board = (new ClassicalStrToBoard($this->params['fen']))->create();
        $filename = (new BoardToPng($board, $this->params['flip'] === Color::B))
          ->output(AbstractSocket::TMP_FOLDER);
        $contents = file_get_contents(AbstractSocket::TMP_FOLDER . "/$filename");
        $base64 = base64_encode($contents);
        if (is_file(AbstractSocket::TMP_FOLDER . "/$filename")) {
            unlink(AbstractSocket::TMP_FOLDER . "/$filename");
        }

        return $base64;
    }
}
