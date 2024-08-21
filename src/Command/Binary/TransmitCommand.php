<?php

namespace ChessServer\Command\Binary;

use ChessServer\Command\AbstractCommand;
use ChessServer\Socket\AbstractSocket;

class TransmitCommand extends AbstractCommand
{
    public function __construct()
    {
        $this->name = '/transmit';
        $this->description = 'Transmission of binary data.';
        $this->params = [
            'settings' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $params = json_decode(stripslashes($argv[1]), true);

        // TODO

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => 'TODO',
        ]);
    }
}
