<?php

namespace ChessServer\Command;

use Spatie\Async\Pool;

abstract class AbstractSyncCommand extends AbstractCommand
{
    protected Pool $pool;

    public function setPool(Pool $pool): AbstractAsyncCommand
    {
        $this->pool = $pool;

        return $this;
    }
}
