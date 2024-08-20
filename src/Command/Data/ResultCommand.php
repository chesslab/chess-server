<?php

namespace ChessServer\Command\Data;

use ChessServer\Socket\AbstractSocket;

class ResultCommand extends AbstractDataCommand
{
    public function __construct(Db $db)
    {
        parent::__construct($db);

        $this->name = '/result';
        $this->description = 'Openings results.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(AbstractSocket $socket, array $argv, int $id)
    {
        $sql = "SELECT ECO, COUNT(*) AS total
          FROM games
          WHERE Result = '1/2-1/2'
          GROUP BY ECO
          HAVING total >= 100
          ORDER BY total DESC
          LIMIT 50";

        $drawRate = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        $sql = "SELECT ECO, COUNT(*) AS total
          FROM games
          WHERE Result = '1-0'
          GROUP BY ECO
          HAVING total >= 100
          ORDER BY total DESC
          LIMIT 50";

        $winRateForWhite = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        $sql = "SELECT ECO, COUNT(*) AS total
          FROM games
          WHERE Result = '0-1'
          GROUP BY ECO
          HAVING total >= 100
          ORDER BY total DESC
          LIMIT 50";

        $winRateForBlack = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        $arr = [
            'drawRate' => $drawRate,
            'winRateForWhite' => $winRateForWhite,
            'winRateForBlack' => $winRateForBlack,
        ];

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => $arr,
        ]);
    }
}
