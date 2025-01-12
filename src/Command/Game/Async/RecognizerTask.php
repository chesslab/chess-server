<?php

namespace ChessServer\Command\Game\Async;

use Chess\Media\ImgToPiecePlacement;
use ChessServer\Command\AbstractSyncTask;

class RecognizerTask extends AbstractSyncTask
{
    public function run()
    {
        $filtered = preg_replace('#^data:image/[^;]+;base64,#', '', $this->params['data']);
        $decoded = base64_decode($filtered);
        $image = imagecreatefromstring($decoded);

        return (new ImgToPiecePlacement($image))->predict();
    }
}
