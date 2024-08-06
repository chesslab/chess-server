<?php

namespace ChessServer\Command\Data;

use \PDO;

/**
 * Pdo class.
 */
class Pdo
{
    /**
     * Pdo instance.
     *
     * @var \ChessApi\Pdo
     */
    private static $instance;

    private string $dsn;

    private PDO $pdo;

    /**
     * Returns the current instance.
     *
     * @param array $conf
     * @return \ChessData\Pdo
     */
    public static function getInstance(array $conf)
    {
        return static::$instance ?? static::$instance = new static($conf);
    }

    /**
     * Constructor.
     *
     * @param array $conf
     */
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

    /**
     * Queries the database.
     *
     * @param string
     * @param array
     * @return bool
     */
    public function query($sql, $values = [])
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
