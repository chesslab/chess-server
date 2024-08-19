<?php

namespace ChessServer\Command\Data;

use ChessServer\Socket\AbstractChesslaBlabSocket;

class StatsOpeningCommand extends AbstractDataCommand
{
    public function __construct(Db $db)
    {
        parent::__construct($db);

        $this->name = '/stats_opening';
        $this->description = 'Openings by result.';
    }

    public function validate(array $argv)
    {
        return count($argv) - 1 === 0;
    }

    public function run(AbstractChesslaBlabSocket $socket, array $argv, int $id)
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
            $this->name => [
              'drawRate' => $drawRate,
              'winRateForWhite' => $winRateForWhite,
              'winRateForBlack' => $winRateForBlack
            ],
        ];

        return $socket->getClientStorage()->sendToOne($id, [
            $this->name => $arr,
        ]);
    }
}
