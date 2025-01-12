<?php

namespace ChessServer\Command;

use Spatie\Async\Pool;

abstract class AbstractBlockingCommand extends AbstractCommand
{
    protected Pool $pool;

    public function setPool(Pool $pool): AbstractBlockingCommand
    {
        $this->pool = $pool;

        return $this;
    }
}
