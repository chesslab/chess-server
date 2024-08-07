<?php

namespace ChessServer\Command\Data;

use \PDO;
use \PDOStatement;

class Db
{
    private static $instance;

    private string $dsn;

    private PDO $pdo;

    public static function getInstance(array $conf)
    {
        return static::$instance ?? static::$instance = new static($conf);
    }

    protected function __construct(array $conf)
    {
        $this->dsn = $conf['driver'] . ':host=' . $conf['host'] . ';dbname=' . $conf['database'];

        $this->pdo = new PDO(
            $this->dsn,
            $conf['username'],
            $conf['password'],
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION],
        );
    }

    /**
     * Prevents from cloning.
     */
    private function __clone()
    {
    }

    /**
     * Prevents from unserializing.
     */
    public function __wakeup()
    {
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
