<?php

namespace ChessServer\Command;

use ChessServer\Command\AcceptPlayRequestCommand;
use ChessServer\Command\DrawCommand;
use ChessServer\Command\LeaveCommand;
use ChessServer\Command\LegalCommand;
use ChessServer\Command\OnlineGamesCommand;
use ChessServer\Command\PlayLanCommand;
use ChessServer\Command\RandomizerCommand;
use ChessServer\Command\RematchCommand;
use ChessServer\Command\ResignCommand;
use ChessServer\Command\RestartCommand;
use ChessServer\Command\StartCommand;
use ChessServer\Command\StockfishCommand;
use ChessServer\Command\TakebackCommand;
use ChessServer\Command\TutorFenCommand;
use ChessServer\Command\UndoCommand;

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
        $this->obj->attach(new TutorFenCommand());
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
