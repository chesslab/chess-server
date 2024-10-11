<?php

namespace ChessServer\Command\Auth\Async;

use ChessServer\Command\AbstractDbAsyncTask;
use OTPHP\InternalClock;
use OTPHP\TOTP;

class TotpSignUpTask extends AbstractDbAsyncTask
{
    public function run()
    {
        $sql = "SELECT username FROM users WHERE lastLoginAt IS NULL ORDER BY RAND() LIMIT 1";

        $username = $this->db->query($sql)->fetchColumn();

        $otp = TOTP::createFromSecret($this->env['totp']['secret'], new InternalClock());
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
