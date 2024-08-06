<?php

namespace ChessServer\Command\Game;

use ChessServer\Command\AbstractCommandContainer;

class CommandContainer extends AbstractCommandContainer
{
    public function __construct()
    {
        $this->obj = new \SplObjectStorage;
        $this->obj->attach(new AcceptPlayRequestCommand());
        $this->obj->attach(new DrawCommand());
        $this->obj->attach(new LeaveCommand());
        $this->obj->attach(new LegalCommand());
        $this->obj->attach(new OnlineGamesCommand());
        $this->obj->attach(new PlayLanCommand());
        $this->obj->attach(new PlayRavCommand());
        $this->obj->attach(new RandomizerCommand());
        $this->obj->attach(new RematchCommand());
        $this->obj->attach(new ResignCommand());
        $this->obj->attach(new RestartCommand());
        $this->obj->attach(new StartCommand());
        $this->obj->attach(new StockfishCommand());
        $this->obj->attach(new TakebackCommand());
        $this->obj->attach(new UndoCommand());
    }
}
