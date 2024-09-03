<?php

namespace ChessServer\Command\Data;

use ChessServer\Socket\AbstractSocket;
use OTPHP\InternalClock;
use OTPHP\TOTP;

class TotpSignUpCommand extends AbstractDataCommand
{
    public function __construct(Db $db)
    {
        parent::__construct($db);

        $this->name = '/totp_signup';
        $this->description = 'TOTP sign up.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $sql = "SELECT username FROM users WHERE lastLoginAt IS NULL ORDER BY RAND() LIMIT 1";

        $username = $this->db->query($sql)->fetchColumn();

        $otp = TOTP::createFromSecret($_ENV['TOTP_SECRET'], new InternalClock());
        $otp->setDigits(9);
        $otp->setLabel($username);
        $otp->setIssuer('ChesslaBlab');
        $otp->setParameter('image', 'https://chesslablab.org/logo.png');

        $arr = [
            'uri' => $otp->getQrCodeUri(
                'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
                '[DATA]'
            )
        ];

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => $arr,
        ]);
    }
}
