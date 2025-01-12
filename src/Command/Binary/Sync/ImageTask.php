<?php

namespace ChessServer\Command\Binary\Sync;

use Chess\Media\BoardToPng;
use Chess\Variant\Classical\FEN\StrToBoard as ClassicalStrToBoard;
use Chess\Variant\Classical\PGN\AN\Color;
use ChessServer\Command\AbstractSyncTask;
use ChessServer\Socket\AbstractSocket;

class ImageTask extends AbstractSyncTask
{
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
