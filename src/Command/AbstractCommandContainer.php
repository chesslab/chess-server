<?php

namespace ChessServer\Command;

abstract class AbstractCommandContainer
{
    protected $obj;

    public function findByName(string $name)
    {
        $this->obj->rewind();
        while ($this->obj->valid()) {
            if ($this->obj->current()->name === $name) {
                return $this->obj->current();
            }
            $this->obj->next();
        }

        return null;
    }

    public function help()
    {
        $o = '';
        $this->obj->rewind();
        while ($this->obj->valid()) {
            $o .= $this->obj->current()->name;
            $this->obj->current()->params ? $o .= ' ' . json_encode($this->obj->current()->params) : null;
            $o .= ' ' . $this->obj->current()->description . PHP_EOL;
            $this->obj->next();
        }

        return $o;
    }
}
