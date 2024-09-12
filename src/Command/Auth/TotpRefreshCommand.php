<?php

namespace ChessServer\Command\Auth;

use ChessServer\Command\AbstractCommand;
use ChessServer\Command\Db;
use ChessServer\Socket\AbstractSocket;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TotpRefreshCommand extends AbstractCommand
{
    public function __construct(Db $db)
    {
        parent::__construct($db);

        $this->name = '/totp_refresh';
        $this->description = 'Refresh the TOTP access token.';
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

        if (isset($params['access_token'])) {
            $decoded = JWT::decode($params['access_token'], new Key($_ENV['JWT_SECRET'], 'HS256'));
            $sql = "SELECT * FROM users WHERE username = :username";
            $values[] = [
              'param' => ":username",
              'value' => $decoded->username,
              'type' => \PDO::PARAM_STR,
            ];
            $arr = $this->db->query($sql, $values)->fetch(\PDO::FETCH_ASSOC);
            $payload = [
              'iss' => $_ENV['JWT_ISS'],
              'iat' => time(),
              'exp' => time() + 3600, // one hour by default
              'username' => $arr['username'],
              'elo' => $arr['elo'],
            ];
            return $socket->getClientStorage()->sendToOne($id, [
              $this->name => [
                  'access_token' => JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256'),
              ],
            ]);
        }

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => null,
        ]);
    }
}
