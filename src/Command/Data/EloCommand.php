<?php

namespace ChessServer\Command\Data;

use ChessServer\Socket\AbstractSocket;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class EloCommand extends AbstractDataCommand
{
    public function __construct(Db $db)
    {
        parent::__construct($db);

        $this->name = '/elo';
        $this->description = 'Updates the ELO of the players after a game has been played.';
        $this->params = [
            'params' => '<string>',
        ];
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === count($this->params);
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $params = json_decode(stripslashes($argv[1]), true);

        $decoded = JWT::decode($params['access'], new Key($_ENV['JWT_SECRET'], 'HS256'));

        // TODO ...

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => 'TODO',
        ]);
    }
}
