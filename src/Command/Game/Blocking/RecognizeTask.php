<?php

namespace ChessServer\Command\Game\Blocking;

use Chess\Media\ImgToPiecePlacement;
use ChessServer\Command\AbstractBlockingTask;

class RecognizeTask extends AbstractBlockingTask
{
    public function run()
    {
        $filtered = preg_replace('#^data:image/[^;]+;base64,#', '', $this->params['data']);
        $decoded = base64_decode($filtered);
        $image = imagecreatefromstring($decoded);

        return (new ImgToPiecePlacement($image))->predict();
    }
}
