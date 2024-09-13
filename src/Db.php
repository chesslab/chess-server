<?php

namespace ChessServer;

use \PDO;
use \PDOStatement;

class Db
{
    private string $dsn;

    private PDO $pdo;

    public function __construct(array $conf)
    {
        $this->dsn = $conf['driver'] . ':host=' . $conf['host'] . ';dbname=' . $conf['database'];

        $this->pdo = new PDO(
            $this->dsn,
            $conf['username'],
            $conf['password'],
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION],
        );
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function query(string $sql, array $values = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);

        foreach ($values as $value) {
            $stmt->bindValue(
                $value['param'],
                $value['value'],
                $value['type'] ?? null
            );
        }

        $stmt->execute();

        return $stmt;
    }
}
