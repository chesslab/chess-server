<?php

namespace ChessServer\Command\Play;

class CommandContainer
{
    private $obj;

    public function __construct()
    {
        $this->obj = new \SplObjectStorage;
        $this->obj->attach(new AcceptPlayRequestCommand());
        $this->obj->attach(new DrawCommand());
        $this->obj->attach(new LeaveCommand());
        $this->obj->attach(new LegalCommand());
        $this->obj->attach(new OnlineGamesCommand());
        $this->obj->attach(new PlayLanCommand());
        $this->obj->attach(new RandomizerCommand());
        $this->obj->attach(new RematchCommand());
        $this->obj->attach(new ResignCommand());
        $this->obj->attach(new RestartCommand());
        $this->obj->attach(new StartCommand());
        $this->obj->attach(new StockfishCommand());
        $this->obj->attach(new TakebackCommand());
        $this->obj->attach(new UndoCommand());
    }

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
