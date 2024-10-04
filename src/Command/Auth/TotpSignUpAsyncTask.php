<?php

namespace ChessServer\Command\Auth;

use ChessServer\Db;
use OTPHP\InternalClock;
use OTPHP\TOTP;
use Spatie\Async\Task;

class TotpSignUpAsyncTask extends Task
{
    private array $conf;

    private array $totp;

    private Db $db;

    public function __construct(array $conf, array $totp)
    {
        $this->conf = $conf;
        $this->totp = $totp;
    }

    public function configure()
    {
        $this->db = new Db($this->conf);
    }

    public function run()
    {
        $sql = "SELECT username FROM users WHERE lastLoginAt IS NULL ORDER BY RAND() LIMIT 1";

        $username = $this->db->query($sql)->fetchColumn();

        $otp = TOTP::createFromSecret($this->totp['secret'], new InternalClock());
        $otp->setDigits(9);
        $otp->setLabel($username);
        $otp->setIssuer('ChesslaBlab');
        $otp->setParameter('image', 'https://chesslablab.org/logo.png');

        return [
            'uri' => $otp->getQrCodeUri(
                'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
                '[DATA]'
            )
        ];
    }
}
