<?php

namespace ChessServer\Command;

use Spatie\Async\Pool;

abstract class AbstractAsyncCommand extends AbstractCommand
{
    protected Pool $pool;

    public function setPool(Pool $pool): AbstractAsyncCommand
    {
        $this->pool = $pool;

        return $this;
    }
}
