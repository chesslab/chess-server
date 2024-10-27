<?php

namespace ChessServer\Command\Game\Async;

use Chess\Media\ImgToPiecePlacement;
use ChessServer\Command\AbstractAsyncTask;

class RecognizerTask extends AbstractAsyncTask
{
    public function run()
    {
        $data = base64_decode($this->params['data']);
        $image = imagecreatefromstring($data);

        return (new ImgToPiecePlacement($image))->predict();
    }
}
