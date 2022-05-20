<?php

namespace ChessServer;

use ChessServer\Command\AcceptPlayRequestCommand;
use ChessServer\Command\DrawCommand;
use ChessServer\Command\HeuristicsCommand;
use ChessServer\Command\HeuristicsBarCommand;
use ChessServer\Command\LegalSqsCommand;
use ChessServer\Command\PlayFenCommand;
use ChessServer\Command\QuitCommand;
use ChessServer\Command\RematchCommand;
use ChessServer\Command\ResignCommand;
use ChessServer\Command\ResponseCommand;
use ChessServer\Command\RestartCommand;
use ChessServer\Command\StartCommand;
use ChessServer\Command\TakebackCommand;
use ChessServer\Command\UndoMoveCommand;

class CommandContainer
{
    private $obj;

    public function __construct()
    {
        $this->obj = new \SplObjectStorage;
        $this->obj->attach(new AcceptPlayRequestCommand());
        $this->obj->attach(new DrawCommand());
        $this->obj->attach(new HeuristicsCommand());
        $this->obj->attach(new HeuristicsBarCommand());
        $this->obj->attach(new LegalSqsCommand());
        $this->obj->attach(new PlayFenCommand());
        $this->obj->attach(new QuitCommand());
        $this->obj->attach(new RematchCommand());
        $this->obj->attach(new ResignCommand());
        $this->obj->attach(new ResponseCommand());
        $this->obj->attach(new RestartCommand());
        $this->obj->attach(new StartCommand());
        $this->obj->attach(new TakebackCommand());
        $this->obj->attach(new UndoMoveCommand());
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
