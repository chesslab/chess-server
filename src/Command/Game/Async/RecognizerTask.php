<?php

namespace ChessServer\Command\Game\Async;

use Chess\Media\FEN\JpgToPiecePlacement;
use ChessServer\Command\AbstractAsyncTask;

class RecognizerTask extends AbstractAsyncTask
{
    public function run()
    {
        $data = base64_decode($this->params['data']);
        $image = imagecreatefromstring($data);

        return (new JpgToPiecePlacement($image))->predict();
    }
}
