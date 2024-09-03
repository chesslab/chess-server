<?php

namespace ChessServer\Command\Data;

use ChessServer\Socket\AbstractSocket;

class QrCommand extends AbstractDataCommand
{
    public function __construct(Db $db)
    {
        parent::__construct($db);

        $this->name = '/qr';
        $this->description = 'QR code URI to sign up.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $sql = "SELECT * FROM users WHERE lastLoginAt IS NULL ORDER BY RAND() LIMIT 1";

        $arr = $this->db->query($sql)->fetch(\PDO::FETCH_ASSOC);

        // TODO ...

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => $arr,
        ]);
    }
}
