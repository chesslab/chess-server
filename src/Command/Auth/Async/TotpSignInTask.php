<?php

namespace ChessServer\Command\Auth\Async;

use ChessServer\Command\AbstractDbAsyncTask;
use Firebase\JWT\JWT;
use OTPHP\InternalClock;
use OTPHP\TOTP;

class TotpSignInTask extends AbstractDbAsyncTask
{
    public function run()
    {
        $otp = TOTP::createFromSecret($this->env['totp']['secret'], new InternalClock());
        $otp->setDigits(9);

        if ($otp->verify($this->params['password'], null, 5)) {
            $sql = "SELECT * FROM users WHERE username = :username";
            $values[] = [
                'param' => ":username",
                'value' => $this->params['username'],
                'type' => \PDO::PARAM_STR,
            ];
            $arr = $this->db->query($sql, $values)->fetch(\PDO::FETCH_ASSOC);

            $sql = "UPDATE users SET lastLoginAt = now() WHERE username = :username";
            $values[] = [
                'param' => ":username",
                'value' => $this->params['username'],
                'type' => \PDO::PARAM_STR,
            ];
            $this->db->query($sql, $values);

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
