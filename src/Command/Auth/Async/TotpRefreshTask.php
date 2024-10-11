<?php

namespace ChessServer\Command\Auth\Async;

use ChessServer\Command\AbstractDbAsyncTask;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TotpRefreshTask extends AbstractDbAsyncTask
{
    public function run()
    {
        if (isset($this->params['access_token'])) {
            $decoded = JWT::decode($this->params['access_token'], new Key($this->env['jwt']['secret'], 'HS256'));
            $sql = "SELECT * FROM users WHERE username = :username";
            $values[] = [
              'param' => ":username",
              'value' => $decoded->username,
              'type' => \PDO::PARAM_STR,
            ];
            $arr = $this->db->query($sql, $values)->fetch(\PDO::FETCH_ASSOC);
            $payload = [
              'iss' => $this->env['jwt']['iss'],
              'iat' => time(),
              'exp' => time() + 3600, // one hour by default
              'username' => $arr['username'],
              'elo' => $arr['elo'],
            ];
            return [
                'access_token' => JWT::encode($payload, $this->env['jwt']['secret'], 'HS256'),
            ];
        }

        return null;
    }
}
