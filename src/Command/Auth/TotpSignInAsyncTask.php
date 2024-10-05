<?php

namespace ChessServer\Command\Auth;

use ChessServer\Db;
use Firebase\JWT\JWT;
use OTPHP\InternalClock;
use OTPHP\TOTP;
use Spatie\Async\Task;

class TotpSignInAsyncTask extends Task
{
    private array $params;

    private array $conf;

    private array $totp;

    private array $jwt;

    private Db $db;

    public function __construct(array $params, array $conf, array $totp, array $jwt)
    {
        $this->params = $params;
        $this->conf = $conf;
        $this->totp = $totp;
        $this->jwt = $jwt;
    }

    public function configure()
    {
        $this->db = new Db($this->conf);
    }

    public function run()
    {
        $otp = TOTP::createFromSecret($this->totp['secret'], new InternalClock());
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
                'iss' => $this->jwt['iss'],
                'iat' => time(),
                'exp' => time() + 3600, // one hour by default
                'username' => $arr['username'],
                'elo' => $arr['elo'],
            ];

            return [
                'access_token' => JWT::encode($payload, $this->jwt['secret'], 'HS256'),
            ];
        }

        return null;
    }
}
